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
