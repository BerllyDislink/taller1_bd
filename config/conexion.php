<?php
$server = "localhost";
$user = "root";
$password = "";
$db_name = "test2";
$port = "3308";

try {
    // Intentar crear la conexión a la base de datos
    $conexion = new mysqli($server, $user, $password, $db_name, $port);

    // Verificar si hay algún error en la conexión
    if ($conexion->connect_error) {
        throw new Exception("Error de conexión a la base de datos: " . $conexion->connect_error);
    }
} catch (Exception $e) {
    // Manejar la excepción y mostrar un mensaje de error
    echo "No se pudo conectar a la base de datos. Error: " . $e->getMessage();
}