<?php

require_once("../../config/conexion.php");
require_once("../../Models/table3/table3_model.php");
require_once("../../view/view_table3.php");

// Obtener el ID de productos proporcionado desde la solicitud HTTP
$productos_id = $_GET['productos_id'] ?? null;

// Verificar si se proporcionó un ID válido
if ($productos_id !== null) {
    // Obtener los datos según el ID proporcionado
    $datos = obtenerDatos($productos_id, $conexion);

    if ($datos !== null) {
        // Se encontraron datos, mostrarlos en formato JSON
        mostrarRespuestaJSON(200, $datos);
    } else {
        // No se encontraron datos para el ID proporcionado
        mostrarErrorJSON(404, "No se encontraron datos para el ID proporcionado");
    }
} else {
    // No se proporcionó un ID válido
    mostrarErrorJSON(400, "Se debe proporcionar un ID existente de la tabla productos");
}

?>
