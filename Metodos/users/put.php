<?php

require_once '../../Models/user_model/user_consulta.php';
require_once '../../response_formatters/Formatter.php';
require_once '../../view/view.php';



// Elegir un formateador según la solicitud o algún criterio
// En este ejemplo, vamos a utilizar JsonFormatter
$formato = new Formatter;

// Verifica si se ha proporcionado un ID de usuario
if (!isset($_GET['usuario_id'])) {
    mostrarRespuesta(400, ['mensaje' => 'ID de usuario es necesario'], $formato);
    exit(); // Termina la ejecución para evitar continuar con el código
}

$usuario_id = $_GET['usuario_id'];

// Obtén los datos de la solicitud `PUT`
$datos = json_decode(file_get_contents("php://input"), true);

// Verifica si los datos están vacíos o no se proporcionaron
if (empty($datos)) {
    mostrarRespuesta(400, ['mensaje' => 'Datos necesarios para actualizar el usuario'], $formato);
    exit(); // Termina la ejecución para evitar continuar con el código
}

// Lógica para actualizar un usuario existente
$exito = actualizarUsuario($conexion, $usuario_id, $datos);

if ($exito) {
    mostrarRespuesta(200, ['mensaje' => 'Usuario actualizado correctamente'], $formato);
} else {
    mostrarRespuesta(500, ['mensaje' => 'Error al actualizar el usuario'], $formato);
}




