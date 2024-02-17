<?php
require 'includes/funciones.php';
incluirTemplate('header');
?>

<main class="contenedor seccion">
    <h1>Iniciar Sesión</h1>
    <form action="" class="formulario contenido-centrado">
        <fieldset>
            <legend>Email y Password</legend>

            <label for="email">E-mail</label>
            <input type="email" placeholder="Tu email" id="email">

            <label for="password">Password</label>
            <input type="tel" placeholder="Tu Password" id="telefono">

            <label for="mensaje">Mensaje</label>
            <textarea name="mensaje" id="mensaje" cols="30" rows="10"></textarea>
        </fieldset>

        <input type="submit" value="Iniciar Sesión" class="boton boton-verde">
    </form>

</main>

<?php
incluirTemplate('footer');
?>