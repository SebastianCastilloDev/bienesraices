<?php
    //base de datos
    require '../../includes/config/database.php';

    $db = conectarDB();

    // Arreglo con mensajes de errores
    $errores = [];
    //Ejecutar el código después de que el usuario envía información.

    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        $titulo = $_POST['titulo'];
        $precio = $_POST['precio'];
        $descripcion = $_POST['descripcion'];
        $habitaciones = $_POST['habitaciones'];
        $wc = $_POST['wc'];
        $estacionamiento = $_POST['estacionamiento'];
        $vendedorId = $_POST['vendedor'];

        if(!$titulo) {
            $errores[] = "Debes añadir un título";
        }
        if(!$precio) {
            $errores[] = "Debes añadir un precio";
        }
        if( strlen($descripcion) < 50) {
            $errores[] = "Debes añadir un descripción y debe tener al menos 50 caracteres";
        }
        if(!$habitaciones) {
            $errores[] = "Debes añadir el número de habitaciones";
        }
        if(!$wc) {
            $errores[] = "Debes añadir un numero de baños";
        }
        if(!$estacionamiento) {
            $errores[] = "Debes añadir un número de estacionamientosl";
        }
        if(!$vendedorId) {
            $errores[] = "Debes elegir un vendedor";
        }

        // echo "<pre>";
        // var_dump($errores);
        // echo "<pre>";

        // Revisar que el arreglo de errores esté vacío
        if (empty($errores)) {
            //Insertar en la base de datos
            $query = " INSERT INTO propiedades (titulo, precio, descripcion, habitaciones, wc, estacionamiento, vendedorId) VALUES ('$titulo', '$precio', '$descripcion', '$habitaciones', '$wc', '$estacionamiento', '$vendedorId')";

            $resultado = mysqli_query($db, $query);

            if($resultado) {
                echo "Insertado correctamente";
            }
        }


        

    }
    
    require '../../includes/funciones.php';
    incluirTemplate('header');
?>

    <main class="contenedor seccion">
        <h1>Crear</h1>
        <a href="/admin" class="boton boton-verde">Volver</a>

        <?php foreach($errores as $error) { ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
        <?php } ?>
        
        
        <form class="formulario" method="POST" action="/admin/propiedades/crear.php">
            <fieldset>
                <legend>Información General</legend>

                <label for="titulo">Titulo:</label>
                <input type="text" id="titulo" name="titulo" placeholder="Título propiedad">
                
                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" placeholder="Precio propiedad">

                <label for="imagen">Imagen:</label>
                <input type="file" id="imagen" name="imagen" accept="image/jpeg, image/png">

                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" cols="30" rows="10"></textarea>

            </fieldset>

            <fieldset>
                <legend>Información Propiedad</legend>

                <label for="habitaciones">Habitaciones:</label>
                <input type="number" id="habitaciones" name="habitaciones" placeholder="Ejemplo: 3" min="1" max="9">

                <label for="wc">Baños:</label>
                <input type="number" id="wc" name="wc" placeholder="Ejemplo: 3" min="1" max="9">

                <label for="estacionamiento">Estacionamiento:</label>
                <input type="text" id="estacionamiento" name="estacionamiento" placeholder="Ejemplo: 2">

            </fieldset>

            <fieldset>
                <legend>Vendedor:</legend>

                <select name="vendedor" id="">
                    <option value="">-- Seleccione --</option>
                    <option value="1">Juan</option>
                    <option value="2">Karen</option>
                </select>
            </fieldset>

            <input type="submit" value="Crear Propiedad" class="boton boton-verde">
        </form>

    </main>

 <?php
    incluirTemplate('footer');
?>