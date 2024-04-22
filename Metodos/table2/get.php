<?php

require_once("../../config/conexion.php");
require_once("../../Models/table2/table2_model.php");
require_once("../../view/view_table2.php");

// Obtener el ID proporcionado desde la solicitud HTTP
$detalles_id = $_GET['detalles_id'] ?? null;

// Verificar si se proporcionó un ID
if ($detalles_id !== null) {
    // Obtener los datos según el ID proporcionado
    $datos = obtenerDatos($detalles_id, $conexion);

    if ($datos !== null) {
        // Se encontraron datos, mostrarlos en formato JSON
        mostrarRespuestaJSON(200, $datos);
    } else {
        // No se encontraron datos para el ID proporcionado
        mostrarErrorJSON(404, "No se encontraron datos para el ID proporcionado");
    }
} else {
    // No se proporcionó un ID válido
    mostrarErrorJSON(400, "Se debe proporcionar un ID existente de la tabla detalles");
}

?>
