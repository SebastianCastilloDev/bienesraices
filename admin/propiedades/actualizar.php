<?php


$id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: /admin');
}

//base de datos
require '../../includes/config/database.php';
$db = conectarDB();

// Obtener los datos de la propiedad
$consultaPropiedad = "SELECT * FROM propiedades WHERE id=$id";
$resultadoPropiedad = mysqli_query($db, $consultaPropiedad);
$propiedad = mysqli_fetch_assoc($resultadoPropiedad);

// Consulta para obtener los vendedores
$consulta = "SELECT * FROM vendedores";
$resultado = mysqli_query($db, $consulta);

// Arreglo con mensajes de errores
$errores = [];

$titulo = $propiedad['titulo'];
$precio = $propiedad['precio'];
$descripcion = $propiedad['descripcion'];
$habitaciones = $propiedad['habitaciones'];
$wc = $propiedad['wc'];
$estacionamiento = $propiedad['estacionamiento'];
$vendedorId = $propiedad['vendedorId'];
$imagenPropiedad = $propiedad['imagen'];

//Ejecutar el código después de que el usuario envía información.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $titulo = mysqli_real_escape_string($db, $_POST['titulo']);
    $precio = mysqli_real_escape_string($db, $_POST['precio']);
    $descripcion = mysqli_real_escape_string($db, $_POST['descripcion']);
    $habitaciones = mysqli_real_escape_string($db, $_POST['habitaciones']);
    $wc = mysqli_real_escape_string($db, $_POST['wc']);
    $estacionamiento = mysqli_real_escape_string($db, $_POST['estacionamiento']);
    $vendedorId = mysqli_real_escape_string($db, $_POST['vendedor']);
    $creado = date('Y/m/d');

    // Asignar files hacia una variable
    $imagen = $_FILES['imagen'];


    if (!$titulo) {
        $errores[] = "Debes añadir un título";
    }
    if (!$precio) {
        $errores[] = "Debes añadir un precio";
    }
    if (strlen($descripcion) < 50) {
        $errores[] = "Debes añadir un descripción y debe tener al menos 50 caracteres";
    }
    if (!$habitaciones) {
        $errores[] = "Debes añadir el número de habitaciones";
    }
    if (!$wc) {
        $errores[] = "Debes añadir un numero de baños";
    }
    if (!$estacionamiento) {
        $errores[] = "Debes añadir un número de estacionamientos";
    }
    if (!$vendedorId) {
        $errores[] = "Debes elegir un vendedor";
    }

    // Limitar a 100kb máximo
    $tamanoMaximoImagen = 1000 * 1000;

    if ($imagen['size'] > $tamanoMaximoImagen) {
        $errores[] = "El tamaño máximo de la imagen es de 1Mb";
    }

    // Revisar que el arreglo de errores esté vacío
    if (empty($errores)) {
        // Subida de archivos
        // Crear carpeta
        // $carpetaImagenes = '../../imagenes/';

        // if (!is_dir($carpetaImagenes)) {
        //     mkdir($carpetaImagenes);
        // }

        // // Generar un nombre único
        // $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

        // // Subir la imagen al servidor
        move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen);


        //Insertar en la base de datos
        $query = "UPDATE propiedades SET titulo = '$titulo', precio = '$precio', descripcion = '$descripcion', habitaciones = $habitaciones, wc = $wc, estacionamiento = $estacionamiento, vendedorId = $vendedorId WHERE id = $id";

        $resultado = mysqli_query($db, $query);

        if ($resultado) {
            // Redireccionar
            // resultado=2 es una actualización.
            header('Location: /admin?resultado=2');
        }
    }
}

require '../../includes/funciones.php';
incluirTemplate('header');
?>

<main class="contenedor seccion">
    <h1>Crear</h1>
    <a href="/admin" class="boton boton-verde">Volver</a>

    <?php foreach ($errores as $error) { ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
    <?php } ?>


    <form class="formulario" method="POST" enctype="multipart/form-data">
        <fieldset>
            <legend>Información General</legend>

            <label for="titulo">Titulo:</label>
            <input type="text" id="titulo" name="titulo" placeholder="Título propiedad" value="<?php echo $titulo; ?>">

            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" placeholder="Precio propiedad" value="<?php echo $precio; ?>">

            <label for="imagen">Imagen:</label>
            <input type="file" id="imagen" name="imagen" accept="image/jpeg, image/png">

            <img src="/imagenes/<?php echo $imagenPropiedad; ?>" alt="" class="imagen-small">

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" cols="30" rows="10"><?php echo $descripcion; ?></textarea>

        </fieldset>

        <fieldset>
            <legend>Información Propiedad</legend>

            <label for="habitaciones">Habitaciones:</label>
            <input type="number" id="habitaciones" name="habitaciones" placeholder="Ejemplo: 3" min="1" max="9" value="<?php echo $habitaciones; ?>">

            <label for="wc">Baños:</label>
            <input type="number" id="wc" name="wc" placeholder="Ejemplo: 3" min="1" max="9" value="<?php echo $wc; ?>">

            <label for="estacionamiento">Estacionamiento:</label>
            <input type="text" id="estacionamiento" name="estacionamiento" placeholder="Ejemplo: 2" value="<?php echo $estacionamiento; ?>">

        </fieldset>

        <fieldset>
            <legend>Vendedor:</legend>

            <select name="vendedor" id="">
                <option value="">-- Seleccione --</option>
                <?php while ($vendedor = mysqli_fetch_assoc($resultado)) : ?>
                    <option <?php echo $vendedorId === $vendedor['id'] ? 'selected' : ''; ?> value="<?php echo $vendedor['id']; ?>"><?php echo $vendedor['nombre'] . " " . $vendedor["apellido"]; ?></option>
                <?php endwhile; ?>

            </select>
        </fieldset>

        <input type="submit" value="Actualizar Propiedad" class="boton boton-verde">
    </form>

</main>

<?php
incluirTemplate('footer');
?>