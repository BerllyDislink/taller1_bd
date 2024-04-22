<?php
require_once("../../Models/user_model/user_consulta.php");
require_once("../../response_formatters/Formatter.php");
require_once("../../view/view.php");

// Crea una instancia de Formatter para formatear la respuesta en formato JSON
$formato = new Formatter();

// Obtén los datos de la solicitud `POST`
$datos = json_decode(file_get_contents("php://input"), true);

// Verifica si no se proporcionaron datos
if (empty($datos)) {
    // Si no se proporcionan datos, devuelve un error
    mostrarRespuesta(400, array("mensaje" => "Los datos son necesarios para crear un usuario"), $formato);
    exit; // Finaliza la ejecución del script
}

// Lógica para crear un nuevo usuario
$exito = crearUsuario($datos, $conexion);
if ($exito) {
    mostrarRespuesta(201, array("mensaje" => "Usuario creado correctamente"), $formato);
} else {
    mostrarRespuesta(500, array("mensaje" => "Error al crear el usuario"), $formato);
}

