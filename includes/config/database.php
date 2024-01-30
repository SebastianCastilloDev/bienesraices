<?php

function conectarDB(){
    $db = mysqli_connect('localhost', 'root', '', 'bienesraices_crud');

    if ($db) {
        echo "Se conectó";
    } else {
        echo "No se conectó";
    }
}
