<?php

// Importar la conexión
require 'includes/config/database.php';
$db = conectarDB();

$errores = [];

// Autenticar al usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<pre>";
    var_dump($_POST);
    echo "</pre>";

    $email = mysqli_real_escape_string($db, filter_var($_POST['email'], FILTER_VALIDATE_EMAIL));
    var_dump($email);

    $password = mysqli_real_escape_string($db, $_POST['password']);

    if (!$email) {
        $errores[] = "El email es obligatorio o no es válido";
    }

    if (!$password) {
        $errores[] = "El password es obligatorio";
    }
}


require 'includes/funciones.php';
incluirTemplate('header');
?>

<main class="contenedor seccion">
    <h1>Iniciar Sesión</h1>
    <form method="POST" action="" class="formulario contenido-centrado">
        <fieldset>
            <legend>Email y Password</legend>

            <label for="email">E-mail</label>
            <input type="email" placeholder="Tu email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="tel" placeholder="Tu Password" id="telefono" name="password" required>

            <label for="mensaje">Mensaje</label>
            <textarea name="mensaje" id="mensaje" cols="30" rows="10"></textarea>
        </fieldset>

        <input type="submit" value="Iniciar Sesión" class="boton boton-verde">
    </form>

</main>

<?php
incluirTemplate('footer');
?>