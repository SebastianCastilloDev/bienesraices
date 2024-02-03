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





