<?php
require_once '../../Models/user_model/user_consulta.php';
require_once '../../response_formatters/Formatter.php';
require_once '../../view/view.php';


// Elige el formateador que deseas utilizar (JSON, HTML, XML)
$formato = new JsonFormatter(); // Cambia a `new HtmlFormatter()` o `new XmlFormatter()` según el formato deseado

// Lógica para obtener usuarios
if (isset($_GET['usuario_id'])) {
    $usuario_id = $_GET['usuario_id'];
    $usuario = obtenerUsuarioPorId($conexion, $usuario_id);
    if ($usuario) {
        mostrarRespuesta(200, $usuario, $formato);
    } else {
        mostrarRespuesta(404, ['mensaje' => 'Usuario no encontrado'], $formato);
    }
} else {
    $usuarios = obtenerUsuarios($conexion);
    mostrarRespuesta(200, $usuarios, $formato);
}



