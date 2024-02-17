# Ejecución local del proyecto

En el directorio del proyecto, ejecutar el siguiente comando:

```bash
php -S localhost:3000
```
En otra terminal ejecutar el siguiente comando:

```bash
npm install
```
Luego ejecutar el siguiente comando:

```bash
gulp
```
también debemos contar con un entorno de ejecución de MySQL.

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

$query = "INSERT INTO propiedades (titulo, precio, imagen, descripcion, habitaciones, wc, estacionamiento, creado, vendedorId) VALUES ('$titulo', '$precio', '$nombreImagen', '$descripcion', '$habitaciones', '$wc', '$estacionamiento', '$creado', '$vendedorId')";
```

Note que en el campo imagen, guardamos el nombre de la imagen generado por la funcionalidad que acabamos de implementar.

## Pasando datos de una vista a otra (mostrar un mensaje de alerta)

Podemos pasar valores a través de un query string. Para ello vamos a modificar la instrucción header de la siguiente manera:

```php
header('Location: /admin?mensaje=MensajeURL');
```


La forma de obtener estos datos en el `admin/index.php` sería la siguiente:

```php
if ($_GET['mensaje']) {
    $mensaje = $_GET['mensaje'];
}
```
Notemos que se accede a los valores de la URL a través de la variable superglobal \$_GET.

Es más recomendable pasar mensajes a través de algún código (un numero) que se procese internamente y no enviar el mensaje de forma explícita en la URL. 

```php
header('Location: /admin?resultado=1');
```

Y en el archivo `admin/index.php` recibiremos ese mensaje por get y lo procesaremos de la siguiente manera:

```php

<?php
    $resultado = $_GET['resultado'] ?? null;
    .
    .
    .
?>

<main class="contenedor seccion">
    .
    .
    .
    <?php if (intval($resultado) === 1) : ?>
        <p class="alerta exito">Anuncio creado correctamente</p>
    <?php endif; ?>
    .
    .
    .
</main>
```
## Listando las propiedades.

En nuestro archivo `admin/index.php` vamos a listar las propiedades que se encuentran en la base de datos. Para ello, vamos a realizar una consulta a la base de datos.

```php
<?php

//Importar la conexión
require '../includes/config/database.php';
$db = conectarDB();

//Escribir el query
$query = "SELECT * FROM propiedades";

//Consultar la base de datos
$resultadoConsulta = mysqli_query($db, $query);
```
Luego vamos a recorrer el resultado de la consulta y vamos a mostrar los datos en la vista. Para ello vamos a utilizar la función `mysqli_fetch_assoc` que nos devolverá un array asociativo con los datos de la base de datos. Esto se escribirá en la sección de tbody de la tabla, iterando sobre el resultado de la consulta y escribiendo los datos en un tr.

El código actual es este:

```php
<tr>
    <td>1</td>
    <td>Casa en la playa</td>
    <td><img src="/imagenes/85bf4c3057dcae5ddbd646c4293fcc9a.jpg" alt="imagen" class="imagen-tabla"></td>
    <td>$1200000</td>
    <td>
        <a href="#" class="boton-rojo-block">Eliminar</a>
        <a href="#" class="boton-amarillo-block">Actualizar</a>
    </td>
</tr>
```

Y el código que vamos a escribir es este:

```php
<tbody>
    <?php while ($propiedad = mysqli_fetch_assoc($resultadoConsulta)) : ?>
        <tr>
            <td><?php echo $propiedad['id']; ?></td>
            <td><?php echo $propiedad['titulo']; ?></td>
            <td><img src="/imagenes/<?php echo $propiedad['imagen']; ?>" alt="imagen" class="imagen-tabla"></td>
            <td>$ <?php echo $propiedad['precio']; ?></td>
            <td>
                <a href="#" class="boton-rojo-block">Eliminar</a>
                <a href="#" class="boton-amarillo-block">Actualizar</a>
            </td>
        </tr>
    <?php endwhile; ?>
</tbody>
```

De esta forma, vamos a mostrar todas las propiedades que se encuentran en la base de datos de forma dinámica.

## Actualizando las propiedades

La actualización es una tarea que se realiza con bastante frecuencia en aplicaciones web. En el caso de nuestro proyecto, vamos a permitir que el usuario actualice las propiedades que se encuentran en la base de datos.

Para saber que propiedad vamos a actualizar debemos pasarle el id de la propiedad a través del query string. Por ejemplo, si queremos actualizar la propiedad con id 1, la URL sería la siguiente:

```php
href="/admin/propiedades/actualizar.php?id=<?php $propiedad['id']; ?>"
```

Esto nos va a permitir extraer el valor de id pasado en la URL a través de la variable superglobal \$_GET. pero debemos validar que el id sea un número entero.

En el archivo actualizar.php vamos a extraer el id de la URL y en caso de que ese id no sea un número entero, vamos a redirigir al usuario a la página de inicio.
```php
$id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: /admin');
}
```

Para obtener los datos de la propiedad lo hacemos de la siguiente manera:

```php
// Obtener los datos de la propiedad
$consultaPropiedad = "SELECT * FROM propiedades WHERE id=${id}";
$resultadoPropiedad = mysqli_query($db, $consultaPropiedad);
$propiedad = mysqli_fetch_assoc($resultadoPropiedad);
```

Luego, vamos a almacenar los datos de la propiedad en variables para poder mostrarlos en la vista.

```php
$titulo = $propiedad['titulo'];
$precio = $propiedad['precio'];
$descripcion = $propiedad['descripcion'];
$habitaciones = $propiedad['habitaciones'];
$wc = $propiedad['wc'];
$estacionamiento = $propiedad['estacionamiento'];
$vendedorId = $propiedad['vendedorId'];
$imagenPropiedad = $propiedad['imagen'];
```
En nuestro html agregamos:
  
```php
  <img src="/imagenes/<?php echo $imagenPropiedad; ?>" alt="" class="imagen-small">
```

### Actualizando la base de datos

En este punto el hecho de tener una imagen obligatoria cambia. Ya que ya se ha subido una imagen previamente al momento de crear la propiedad. Por lo tanto, si no se sube una imagen, no se debe borrar la imagen anterior.

Vamos a actualizar la consulta de la base de datos a la siguiente expresión:

```php
$query = "UPDATE propiedades SET titulo = '$titulo', precio = '$precio', descripcion = '$descripcion', habitaciones = $habitaciones, wc = $wc, estacionamiento = $estacionamiento, vendedorId = $vendedorId WHERE id = $id";
```
**NOTA: SIEMPRE DEBEMOS COMPROBAR NUESTRAS QUERYS ANTES DE EJECUTARLAS EN EL CÓDIGO.** 

A continuación modificaremos el valor de resultado enviado a /admin con un valor de 2 de la siguiente manera:

```php
if ($resultado) {
    // Redireccionar
    // resultado=2 es una actualización.
    header('Location: /admin?resultado=2');
}
```
Finalmente agregaremos el siguiente fragmento para nuestro mensaje de alerta en el archivo `admin/index.php` de la siguiente manera:

```php
<?php elseif (intval($resultado) === 2) : ?>
  <p class="alerta exito">Anuncio actualizado correctamente</p>
```

### Eliminando la imagen previa
Si no eliminamos las imagenes previas, estas se acumularán en el servidor. Para eliminar la imagen previa, vamos a utilizar la función `unlink` de PHP. Esta función recibe como parámetro la ruta de la imagen que queremos eliminar.

```php
if($imagen['name']){
  // Eliminar la imagen previa
  unlink($carpetaImagenes . $propiedad['imagen']);
}
```

En caso de que no se suba una imagen, no debemos eliminar la imagen previa. Adaptaremos el código de la siguiente manera:

```php
$nombreImagen = '';

if ($imagen['name']) {
    // Eliminar la imagen previa
    unlink($carpetaImagenes . $propiedad['imagen']);
    // Generar un nombre único
    $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

    // Subir la imagen al servidor
    move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen);
} else {
    $nombreImagen = $propiedad['imagen'];
}
```

## Eliminando propiedades

Adaptaremos nuestro td en el archivo `admin/index.php` de la siguiente manera:

```php
<td>
    <form method="POST" class="w-100">
        <input type="hidden" name="id" value="<?php echo $propiedad['id']; ?>">
        <input type="submit" class="boton-rojo-block" value="Eliminar">
    </form>
    <a href="/admin/propiedades/actualizar.php?id=<?php echo $propiedad['id']; ?>" class="boton-amarillo-block">Actualizar</a>
</td>
```

Recordemos que al no poner un action en el formulario, este se enviará a la misma página. Por lo tanto, en el archivo `admin/index.php` vamos a recibir el id de la propiedad a eliminar y vamos a eliminarla de la base de datos. 

Capturaremos ese id de la siguiente forma:

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
}
```

Esto se hace de esta forma porque mientras no exista ese Request Method, no existirá la superglobal \$_POST.

Este código es vulnerable ya que si inyectamos SQL en la propiedad value del input, Podríamos incluso eliminar la base de datos. 

```php
<input type="hidden" name="id" value="delete * from propiedades;">
```

**NOTA: SIEMPRE DEBEMOS SANITIZAR Y VALIDAR PARÁMETROS QUE INTERACTÚEN CON LA BASE DE DATOS. Aunque estos no se le pidan al usuario**

Para evitar esto, vamos a utilizar la función `filter_var` de la siguiente manera:

```php
$id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
```

Teniendo en cuenta los aspectos anteriores construiremos la funcionalidad para eliminar la propiedad de la base de datos en dos partes: 

La primera es eliminar el archivo de imagen del servidor. Para ello, vamos a obtener el nombre de la imagen de la base de datos y vamos a eliminarla con la función `unlink` de PHP.

```php
//Eliminar el archivo
$query = "SELECT imagen FROM propiedades WHERE id = $id";
$resultado = mysqli_query($db, $query);
$propiedad = mysqli_fetch_assoc($resultado);

unlink('../imagenes/' . $propiedad['imagen']);
```

La segunda parte es eliminar la propiedad de la base de datos. Y lo haremos de la siguiente forma:

```php
//Eliminar la propiedad
$query = "DELETE FROM propiedades WHERE id = $id";
$resultado = mysqli_query($db, $query);
if ($resultado) {
    header('Location: /admin?resultado=3');
}
```

Utilizaremos el valor 3 para indicar que se ha eliminado una propiedad. Una vez hecho esto podemos evaluar este resultado en el archivo `admin/index.php` de la siguiente manera:

```php
<?php elseif (intval($resultado) === 3) : ?>
    <p class="alerta exito">Anuncio eliminado correctamente</p>
```

Finalmente nuestra funcionalidad de eliminar propiedades estará completa y queda de la siguiente manera:

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    if ($id) {

        //Eliminar el archivo
        $query = "SELECT imagen FROM propiedades WHERE id = $id";
        $resultado = mysqli_query($db, $query);
        $propiedad = mysqli_fetch_assoc($resultado);

        unlink('../imagenes/' . $propiedad['imagen']);

        //Eliminar la propiedad
        $query = "DELETE FROM propiedades WHERE id = $id";
        $resultado = mysqli_query($db, $query);
        if ($resultado) {
            header('Location: /admin?resultado=3');
        }
    }
}
```

## Propiedades de la página principal y anuncios dinámicos.

Nota: Se debe revisar los commits para ver los cambios realizados en el código fuente.

Se debe tener en cuenta que en el archivo partial `includes/templates/anuncios.php` las rutas de los require son relativas al archivo que llama al partial, en este caso es el archivo `index.php` que se encuentra en la carpeta raíz. Por lo tanto, las rutas de los require en el archivo `anuncios.php` deben ser relativas a la carpeta raíz.

Para solucionar esto podemos especificar la ruta absoluta de la siguiente manera:

```php
require __DIR__ . '/../config/database.php';
```

Mostraremos 3 propiedades en nuestra landing page. Para ello, vamos a realizar una consulta a la base de datos y vamos a mostrar los datos en la vista. En nuestro index.php vamos a realizar la consulta de la siguiente manera:

```php
<?php
// Importar la conexion
require __DIR__ . '/../config/database.php';
$db = conectarDB();

// Consultar
$query = "SELECT * FROM propiedades LIMIT $limite";

// Obtener resultados
$resultado = mysqli_query($db, $query);

?>

div class="contenedor-anuncios">
    <?php while ($propiedad = mysqli_fetch_assoc($resultado)) : ?>
        <div class="anuncio">

            <img loading="lazy" src="/imagenes/<?php echo $propiedad['imagen']; ?>" alt="anuncio">

            <div class="contenido-anuncio">
                <h3><?php echo $propiedad['titulo']; ?></h3>
                <p><?php echo $propiedad['descripcion']; ?></p>
                <p class="precio">$ <?php $propiedad['precio'] ?></p>

                <ul class="iconos-caracteristicas">
                    <li>
                        <img class="icono" loading="lazy" src="build/img/icono_wc.svg" alt="icono wc">
                        <p><?php echo $propiedad['wc'] ?></p>
                    </li>
                    <li>
                        <img class="icono" loading="lazy" src="build/img/icono_estacionamiento.svg" alt="icono estacionamiento">
                        <p><?php echo $propiedad['estacionamiento'] ?></p>
                    </li>
                    <li>
                        <img class="icono" loading="lazy" src="build/img/icono_dormitorio.svg" alt="icono habitaciones">
                        <p><?php echo $propiedad['habitaciones'] ?></p>
                    </li>
                </ul>

                <a href="anuncio.php?id=<?php echo $propiedad['id']; ?>" class="boton-amarillo-block">
                    Ver Propiedad
                </a>
            </div><!--.contenido-anuncio-->
        </div><!--anuncio-->
    <?php endwhile; ?>
</div> <!--.contenedor-anuncios-->
```

En el caso de la ruta /anuncios.php vamos a realizar la misma consulta a la base de datos y vamos a mostrar los datos en la vista. En nuestro anuncios.php vamos a mostrar los datos de la siguiente manera:

```php
<?php
require 'includes/funciones.php';
incluirTemplate('header');
?>

<main class="contenedor seccion">
    <h2>Casas y Depas en Venta</h2>
    <?php
    $limite = 10;
    include 'includes/templates/anuncios.php';
    ?>
</main>

<?php
incluirTemplate('footer');
?>
```

## Página de anuncio individual

Para mostrar un anuncio individual, vamos a recibir el id de la propiedad a través de la URL. En nuestro template de anuncios.php realizaremos el siguiente cambio:

```php
<a href="anuncio.php?id=<?php echo $propiedad['id']; ?>" class="boton-amarillo-block">
```

Para recibir el id de la propiedad en el archivo anuncio.php vamos a hacer lo siguiente:

```php
<?php

$id = $_GET['id'];
$id = filter_var($id, FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: /');
}

// Importar la conexion
require __DIR__ . '/includes/config/database.php';
$db = conectarDB();

// Consultar
$query = "SELECT * FROM propiedades WHERE id = $id";

// Obtener la propiedad
$resultado = mysqli_query($db, $query);
$propiedad = mysqli_fetch_assoc($resultado);

// RESTO DEL CÓDIGO
```

Si ingresamos un id que no existe, la página nos mostrará un error. Para solucionar esto, vamos a validar con la propiedad num_rows de la consulta. Si el número de filas es igual a 0, vamos a redirigir al usuario a la página principal.

```php
$resultado = mysqli_query($db, $query);

if ($resultado->num_rows===0) {
    header('Location: /');
}
```

## Autenticacion

Planificaremos la autenticación teniendo en cuenta lo siguiente:

- PHP contiene funciones para hashear passwords.
- PHP contiene funciones para verificar si un password hasheado es igual a otro password.
- Un password hasheado es un password que ha sido transformado a través de un algoritmo de hash. El hash es un valor que representa un string de texto, y es único para cada string. Si el string cambia, el hash cambia. Si el string es el mismo, el hash es el mismo. **El hash es irreversible.**
- Si el usuario olvida su password, se le debe enviar un link para que pueda cambiar su password. No se le debe enviar su password original. El usuario deberá cambiar su password.

**NOTA: Este no será un sistema completo de autenticación.**

### Creando una tabla para los usuarios

Durante este proceso vamos a crear un archivo llamado `usuario.php` en la raiz de nuestro proyecto, dentro de este archivo seguiremos los siguientes pasos:

1. Importar la conexión a la base de datos.
2. Crear un email y password para el usuario.
3. Query para crear el usuario.
4. Agregarlo a la base de datos.

De forma paralela, vamos a crear una tabla en la base de datos llamada `usuarios` con los siguientes campos:

id          int
email       varchar(50)
password    char(60)

Nuestro archivo usuario.php quedará inicialmente de la siguiente manera:

```php
<?php

// Importar la conexión
require 'includes/config/database.php';
$db = conectarDB();

// Crear un email y password
$email = "correo@correo.com";
$password = "123456";

// Query para crear el usuario
$query = "INSERT INTO usuarios (email, password) VALUES ( '$email', '$password'); ";

echo $query;

// Agregarlo a la base de datos
mysqli_query($db, $query);
```

### Hasheando passwords

Para hashear passwords vamos a utilizar la función `password_hash` de PHP. Esta función recibe dos parámetros, el password y el algoritmo de hash. El algoritmo de hash que vamos a utilizar es PASSWORD_DEFAULT. Este algoritmo es el recomendado por PHP y es el que se encuentra en uso por defecto. Tambien existe PASSWORD_BCRYPT.

Este hash es un string de 60 caracteres. 

Realizaremos la siguiente modificación en nuestro archivo usuario.php:

```php
<?php

// Importar la conexión
require 'includes/config/database.php';
$db = conectarDB();

// Crear un email y password
$email = "correo@correo.com";
$password = "123456";

// Hashear el password
$passwordHash = password_hash($password, PASSWORD_DEFAULT);
// Query para crear el usuario
$query = "INSERT INTO usuarios (email, password) VALUES ( '$email', '$passwordHash'); ";

echo $query;

exit;
// Agregarlo a la base de datos
mysqli_query($db, $query);
```
Este fragmento de código puede servir como un snippet base para registrar un usuario.

Al consultar la base de datos, veremos que el password se encuentra hasheado.

```
mysql> select * from usuarios;
+----+-------------------+--------------------------------------------------------------+
| id | email             | password                                                     |
+----+-------------------+--------------------------------------------------------------+
|  6 | correo@correo.com | $2y$10$I2QKcg7zhAhZ7Odi4CQY8uHbTS2bwbh/aWfbd55cvJHOB9sOo6Fjy |
+----+-------------------+--------------------------------------------------------------+
```
