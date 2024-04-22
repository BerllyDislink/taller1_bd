<?php
require_once '../../Models/user_model/user_consulta.php';
require_once '../../response_formatters/Formatter.php';
require_once '../../view/view.php';

$formato = new Formatter();

// Verificar si se ha proporcionado un usuario_id
if (!isset($_GET['usuario_id'])) {
    mostrarRespuesta(400, ['mensaje' => 'No se ha proporcionado un ID de usuario'], $formato);
    exit(); // Termina la ejecución para evitar continuar con el código
}

$usuario_id = $_GET['usuario_id'];
$exito = eliminarUsuario($usuario_id, $conexion);

if ($exito) {
    mostrarRespuesta(200, ['mensaje' => 'Usuario eliminado correctamente'], $formato);
} else {
    mostrarRespuesta(500, ['mensaje' => 'Error al eliminar el usuario'], $formato);
}



