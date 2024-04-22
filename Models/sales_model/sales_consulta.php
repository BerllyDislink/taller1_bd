<?php

require_once ("../../config/conexion.php");

// Función para crear una nueva venta
function crearVenta($datosVenta, $conexion)
{
    // Verificar si los datos están en formato JSON
    if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
        http_response_code(400); // Bad Request
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Los datos deben enviarse en formato JSON"), JSON_PRETTY_PRINT);
        return false;
    }

    $precio = $datosVenta['precio'] ?? '';
    $fecha_venta = $datosVenta['fecha_venta'] ?? date('Y-m-d H:i:s'); // Obtener la fecha y hora actual
    $cantidad_venta = $datosVenta['cantidad_venta'] ?? '';

    // Verificar si todos los campos requeridos están presentes
    if (empty($precio) || empty($cantidad_venta)) {
        http_response_code(400); // Bad Request
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Todos los campos son requeridos"), JSON_PRETTY_PRINT);
        return false;
    }

    // Insertar la nueva venta en la base de datos
    $sql = "INSERT INTO ventas (precio, fecha_venta, cantidad_venta) VALUES ($precio, '$fecha_venta', $cantidad_venta)";

    if ($conexion->query($sql) === TRUE) {
        http_response_code(201); // Creado

        return true;
    } else {
        http_response_code(500); // Error del servidor

        return false;
    }
}

// Función para obtener ventas
function obtenerVentas($conexion, $ventas_id = null)
{
    $ventas = array();

    if ($ventas_id !== null) {
        // Obtener una venta por su ID
        $sql = "SELECT * FROM ventas WHERE ventas_id = $ventas_id";
        $resultado = $conexion->query($sql);

        if ($resultado->num_rows > 0) {
            $ventas[] = $resultado->fetch_assoc(); // Aquí se empaqueta dentro de un array
        }
    } else {
        // Obtener todas las ventas
        $sql = "SELECT * FROM ventas";
        $resultado = $conexion->query($sql);

        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $ventas[] = $fila;
            }
        }
    }

    return $ventas;
}

// Función para actualizar una venta existente
function actualizarVenta($conexion, $ventas_id, $datosVenta) {
    // Verifica si los datos están en formato JSON
    if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
        http_response_code(400);
        echo json_encode(array("error" => "Solo se acepta formato JSON"), JSON_PRETTY_PRINT);
        return false;
    }

    // Verifica si el ID está definido
    if (empty($ventas_id)) {
        http_response_code(400);
        echo json_encode(array("error" => "ID de venta no proporcionado"), JSON_PRETTY_PRINT);
        return false;
    }

    // Extrae los campos de datos de la venta
    $precio = isset($datosVenta['precio']) ? (float)$datosVenta['precio'] : null;
    $cantidad_venta = isset($datosVenta['cantidad_venta']) ? (int)$datosVenta['cantidad_venta'] : null;
    $fecha_venta = isset($datosVenta['fecha_venta']) ? $datosVenta['fecha_venta'] : null;

    // Verifica si todos los campos requeridos están presentes
    if (is_null($precio) || is_null($cantidad_venta) || is_null($fecha_venta)) {
        http_response_code(400);
        echo json_encode(array("error" => "Todos los campos son requeridos"), JSON_PRETTY_PRINT);
        return false;
    }

    // Verifica si la venta con el ID proporcionado existe
    $venta_existente = obtenerVentas($conexion, $ventas_id);
    if (empty($venta_existente)) {
        http_response_code(404);
        echo json_encode(array("error" => "Venta no encontrada en la base de datos"), JSON_PRETTY_PRINT);
        return false;
    }

    // Prepara la consulta SQL para actualizar la venta
    $sql = "UPDATE ventas SET precio = ?, fecha_venta = ?, cantidad_venta = ? WHERE ventas_id = ?";
    $stmt = $conexion->prepare($sql);

    // Verifica si la preparación de la consulta fue exitosa
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(array("error" => "Error al preparar la actualización de la venta"), JSON_PRETTY_PRINT);
        return false;
    }

    // Asocia los valores a la consulta preparada
    $stmt->bind_param("dsis", $precio, $fecha_venta, $cantidad_venta, $ventas_id);

    // Ejecuta la consulta preparada
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(array("mensaje" => "Venta actualizada correctamente"), JSON_PRETTY_PRINT);
        return true;
    } else {
        http_response_code(500);
        echo json_encode(array("error" => "Error al actualizar la venta: " . $stmt->error), JSON_PRETTY_PRINT);
        return false;
    }
}









// Función para eliminar una venta
function eliminarVenta($ventas_id, $conexion)
{
    $sql = "DELETE FROM ventas WHERE ventas_id=$ventas_id";

    if ($conexion->query($sql) === TRUE) {

        return true;
    } else {

        return false;
    }
}