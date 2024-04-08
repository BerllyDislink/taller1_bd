<?php

require_once("../config/conexion.php");

// Función para obtener los datos según el ID proporcionado
function obtenerDatos($productos_id, $conexion) {
    // Preparar la consulta SQL con una sentencia parametrizada
    $sql = "SELECT 
                d.detalles_id, d.cantidad, d.fecha_actualizacion,
                p.productos_id, p.nombre AS nombre_producto, p.precio
            FROM 
                detalles d
                JOIN productos p ON d.productos_id = p.productos_id
            WHERE 
                d.productos_id = ?";

    // Preparar la sentencia
    $stmt = $conexion->prepare($sql);

    // Asociar el parámetro
    $stmt->bind_param("i", $productos_id);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener el resultado
    $resultado = $stmt->get_result();

    // Verificar si se obtuvieron resultados
    if ($resultado->num_rows > 0) {
        // Obtener todos los registros
        $filas = $resultado->fetch_all(MYSQLI_ASSOC);
        return $filas;
    } else {
        return null; // No se encontraron resultados
    }

    // Cerrar la sentencia
    $stmt->close();
}

// Obtener el ID de productos proporcionado desde la solicitud HTTP
$productos_id = $_GET['productos_id'] ?? null;

// Verificar si se proporcionó un ID válido
if ($productos_id !== null) {
    // Obtener los datos según el ID proporcionado
    $datos = obtenerDatos($productos_id, $conexion);

    if ($datos !== null) {
        // Se encontraron datos, mostrarlos en formato JSON
        header('Content-Type: application/json');
        echo json_encode($datos, JSON_PRETTY_PRINT);
    } else {
        // No se encontraron datos para el ID proporcionado
        http_response_code(404); // Not Found
        header('Content-Type: application/json');
        echo json_encode(array("error" => "No se encontraron datos para el ID proporcionado"), JSON_PRETTY_PRINT);
    }
} else {
    // No se proporcionó un ID válido
    http_response_code(400); // Bad Request
    header('Content-Type: application/json');
    echo json_encode(array( "Se debe proporcionar un ID existente"), JSON_PRETTY_PRINT);
}

?>
