<?php

function conectarDB() : mysqli{
    $db = mysqli_connect('127.0.0.1', 'root', '', 'bienesraices_crud');



    if (!$db) {
        echo "No se pudo conectar";
        exit;
    } 
    return $db;
}
