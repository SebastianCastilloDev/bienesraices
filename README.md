# Proyecto PHP de Bienes raíces

Este documento contiene las explicaciones acerca de la confeccion de un proyecto con PHP, HTML, CSS y MySQL. Desde la sanitización de datos de entrada en adelante. Contempla 3 tipos de arquitectura. Codigo Espaguetti, POO y MVC. A lo largo de este Readme se irá explicando como se realizó el proyecto, **es importante para ello revisar cada commit para poder ver los cambios realizados en este readme y en el código fuente.**

Nota: No contiene las explicaciones acerca de la instalación de los programas necesarios para el desarrollo del proyecto. ni tampoco la explicación de como generar la base de datos. Tampoco contiene explicaciones acerca de como se realizó el diseño de la página web. Finalmente, no contiene explicaciones sobre como se construyó el proyecto base, ya que es una arquitectura básica disponible en cualquier tutorial.

## Sanitización de datos de entrada


En el ámbito del desarrollo web y de aplicaciones móviles, se encuentra comúnmente la tarea de trabajar con formularios interactivos, en los cuales los usuarios ingresan datos. Es fundamental asegurar la integridad y seguridad de la aplicación ante posibles intentos de inyección de código malicioso o manipulación de la base de datos. Para abordar este desafío, se recurre a prácticas como la sanitización y validación de datos.

La sanitización consiste en limpiar y transformar los datos proporcionados por los usuarios en entidades seguras, evitando posibles daños a la base de datos. En PHP, se emplea la función filter_var junto con distintos filtros de saneamiento, como FILTER_SANITIZE_NUMBER_INT para conservar únicamente números enteros o FILTER_SANITIZE_EMAIL para garantizar la validez de las direcciones de correo electrónico.

Por otro lado, la validación se ejecuta con el objetivo de asegurar que los datos cumplan con criterios específicos, como ser un número entero o una dirección de correo electrónico válida. La función filter_var, utilizando filtros de validación como FILTER_VALIDATE_INT o FILTER_VALIDATE_EMAIL, se utiliza para este propósito.

En https://www.php.net/manual/es/filter.filters.php podemos ver la lista de filtros de saneamiento y validación disponibles en PHP.

**Los sanitizadores van a limpiar los datos de entrada, es decir, van a eliminar cualquier caracter que no sea válido para el tipo de dato que se espera. Por ejemplo, si se espera un número entero, se eliminarán todos los caracteres que no sean números. Si se espera un correo electrónico, se eliminarán todos los caracteres que no sean válidos para un correo electrónico.**

**Los validadores van a verificar que los datos de entrada cumplan con ciertos criterios. Por ejemplo, que un número entero sea realmente un número entero, que un correo electrónico sea realmente un correo electrónico.** En caso de que no cumplan con los criterios, la función devolverá false.

Se destaca la importancia de la función `mysqli_real_escape_string` al interactuar con bases de datos en entornos tradicionales, permitiendo escapar los datos y prevenir posibles inyecciones de código SQL. Esta función es especialmente útil en aplicaciones que no emplean PDO (PHP Data Objects) para la conexión con la base de datos. En el caso de PDO, se recomienda el uso de sentencias preparadas para evitar la inyección de código SQL. 

### Uso de la función `mysqli_real_escape_string`

```php
$nombre = mysqli_real_escape_string($db, $_POST['nombre']);
```
**NOTA: NUNCA CONFIES EN LOS DATOS DE ENTRADA (para no decir usuarios), SIEMPRE SANITIZA Y VALIDA LOS DATOS.**

## Subida de imagenes al servidor

NUNCA subir archivos binarios a la base de datos, siempre subirlos al servidor y guardar la ruta en la base de datos.

Consideremos el siguiente fragmento de código:

```html
<label for="imagen">Imagen:</label>
<input type="file" id="imagen" name="imagen" accept="image/jpeg, image/png">
```

al hacer un var_dump de $_POST obtendremos este resultado:

```php
array(8) {
...
  ["imagen"]=>
  string(14) "destacada3.jpg"
...
}
```
Esto pasa porque el archivo no se sube a la base de datos, sino que se sube al servidor. Por lo tanto, el archivo no se encuentra en \$_POST, sino en \$_FILES.

Para poder aceptar archivos, se debe agregar el atributo enctype="multipart/form-data" al formulario.

```html
<form action="/admin/propiedades/crear.php" method="post" enctype="multipart/form-data">
``` 

con esto, al hacer un var_dump de \$_FILES obtendremos este resultado:

```php
array(1) {
  ["imagen"]=>
  array(6) {
    ["name"]=>
    string(14) "destacada2.jpg"
    ["full_path"]=>
    string(14) "destacada2.jpg"
    ["type"]=>
    string(10) "image/jpeg"
    ["tmp_name"]=>
    string(66) "/private/var/folders/5f/0_dj_sms5dsgmdmvdt749_mw0000gn/T/phpJZNxcJ"
    ["error"]=>
    int(0)
    ["size"]=>
    int(402453)
  }
}
```
Con este resultado podemos hacer varias validaciones, como por ejemplo, si el archivo es una imagen, si el archivo pesa menos de 1MB, si el archivo no tiene errores, etc.

Por ejemplo, si quisieramos validar que la imagen pese menos de 100 Kb, podríamos hacer lo siguiente:

```php
// Limitar a 100kb máximo
$tamanoMaximoImagen = 1000 * 100;

if ($imagen['size'] > $tamanoMaximoImagen) {
    $errores[] = "El tamaño máximo de la imagen es de 100kb";
}
```
Nota: La elección entre 1000 y 1024 para la conversión de bytes a kilobytes depende de la interpretación del kilobyte. La convención histórica y estándar en sistemas de almacenamiento y transferencia de datos utiliza 1 kilobyte como 1024 bytes. Sin embargo, en algunos contextos, especialmente en el ámbito de la informática, se utiliza la definición del Sistema Internacional de Unidades (SI), donde 1 kilobyte es exactamente 1000 bytes.

## Tamaños de imagen superior a 2 Mb.

Si se intenta subir una imagen superior a 2 Mb, se obtendrá un error de tipo 1. Podemos agregar esto a nuestra validacion:

```php
if (!$imagen['name'] || $imagen['error']) {
    $errores[] = "Debes subir una imagen";
}
```

### Modificando el archivo php.ini para permitir subir archivos de mayor tamaño

Para solucionar esto, se debe modificar el archivo php.ini. En el archivo php.ini modificaremos la directiva upload_max_filesize y post_max_size. Por ejemplo, si se quisiera subir archivos de hasta 10 Mb, se debería modificar el archivo php.ini de la siguiente manera:

```php
upload_max_filesize = 10M
post_max_size = 10M
```

Con la instrucción `php --ini` se puede ver la ubicación del archivo php.ini.

### Almacenamiento de la imagen en el servidor

Como criterio para guardar imagenes en el servidor, lo haremos cuando no existan errores, en nuestro caso cuando el array de errores esté vacío.

```php
if (empty($errores)) {
        // Subida de archivos
        // Crear carpeta
        $carpetaImagenes = '../../imagenes/';

        if (!is_dir($carpetaImagenes)) {
            mkdir($carpetaImagenes);
        }

        // Subir la imagen al servidor
        move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $imagen['name']);
        .
        .
        .
}
```

Nos valdremos de las funciones `is_dir` y `mkdir` para crear la carpeta en caso de que no exista. Luego, con la función `move_uploaded_file` moveremos el archivo al servidor.


### Generando nombres únicos para las imágenes

Si se suben dos imágenes con el mismo nombre, la segunda imagen sobreescribirá a la primera. Para evitar esto, se puede generar un nombre único para cada imagen. Para ello, se puede utilizar la función `uniqid` de PHP.

```php
  // Generar un nombre único
        $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

        // Subir la imagen al servidor
        move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen);
```

Nos valdremos de la función `md5` para generar un hash único, y de la función `uniqid` para generar un identificador único. Luego, concatenaremos ambos valores y le agregaremos la extensión del archivo.

NOTA: No utilizar md5 para funciones de seguridad, ya que es vulnerable a ataques de colisión. En este caso, no es un problema, ya que no estamos utilizando md5 para funciones de seguridad. 

Finalmente actualizaremos nuestra consulta SQL para guardar el nombre de la imagen en la base de datos.

```php

$query = " INSERT INTO propiedades (titulo, precio, imagen, descripcion, habitaciones, wc, estacionamiento, creado, vendedorId) VALUES ('$titulo', '$precio', '$nombreImagen', '$descripcion', '$habitaciones', '$wc', '$estacionamiento', '$creado', '$vendedorId')";
```

Note que en el campo imagen, guardamos el nombre de la imagen generado por la funcionalidad que acabamos de implementar.