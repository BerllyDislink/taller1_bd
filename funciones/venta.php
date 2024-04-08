<?php

require_once ("../config/conexion.php");

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
        header('Content-Type: application/json');
        echo json_encode(array("mensaje" => "Venta creada correctamente"), JSON_PRETTY_PRINT);
        return true;
    } else {
        http_response_code(500); // Error del servidor
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Error al crear la venta"), JSON_PRETTY_PRINT);
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
function actualizarVenta($ventas_id, $datosVenta, $conexion)
{
    // Verificar si los datos están en formato JSON
    if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
        http_response_code(400); // Bad Request
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Solo se acepta formato JSON"), JSON_PRETTY_PRINT);
        return false;
    }

    // Verificar si el ID está definido
    if (!isset($ventas_id)) {
        http_response_code(400); // Bad Request
        header('Content-Type: application/json');
        echo json_encode(array("error" => "ID de venta no proporcionado"), JSON_PRETTY_PRINT);
        return false;
    }

    $precio = $datosVenta['precio'] ?? '';
    $fecha_venta = $datosVenta['fecha_venta'] ?? date('Y-m-d H:i:s');
    $cantidad_venta = $datosVenta['cantidad_venta'] ?? '';

    // Verificar si todos los campos requeridos están presentes
    if (empty($precio) || empty($cantidad_venta)) {
        http_response_code(400); // Bad Request
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Todos los campos son requeridos"), JSON_PRETTY_PRINT);
        return false;
    }

    // Verificar si la venta con el ID proporcionado existe
    $venta_existente = obtenerVentas($conexion, $ventas_id);
    if (empty($venta_existente)) {
        http_response_code(404); // Not Found
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Venta no encontrada en la base de datos"), JSON_PRETTY_PRINT);
        return false;
    }

    $sql = "UPDATE ventas SET precio=$precio, fecha_venta='$fecha_venta', cantidad_venta=$cantidad_venta WHERE ventas_id=$ventas_id";

    if ($conexion->query($sql) === TRUE) {
        header('Content-Type: application/json');
        echo json_encode(array("mensaje" => "Venta actualizada correctamente"), JSON_PRETTY_PRINT);
        return true;
    } else {
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Error al actualizar la venta: " . $conexion->error), JSON_PRETTY_PRINT);
        return false;
    }
}



// Función para eliminar una venta
function eliminarVenta($ventas_id, $conexion)
{
    $sql = "DELETE FROM ventas WHERE ventas_id=$ventas_id";

    if ($conexion->query($sql) === TRUE) {
        header('Content-Type: application/json');
        echo json_encode(array("mensaje" => "Venta eliminada correctamente"), JSON_PRETTY_PRINT);
        return true;
    } else {
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Error al eliminar la venta"), JSON_PRETTY_PRINT);
        return false;
    }
}

// Manejo de las solicitudes HTTP

// Obtener el método de la solicitud HTTP
$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'POST':
        // Crear una nueva venta
        $datos = json_decode(file_get_contents("php://input"), true);
        if (crearVenta($datos, $conexion)) {
            http_response_code(201); // Creado
        } else {
            http_response_code(500); // Error del servidor
        }
        break;
    case 'GET':
        // Obtener una venta por su ID si se proporciona
        if (isset($_GET['ventas_id'])) {
            $ventas_id = $_GET['ventas_id'];
            $venta = obtenerVentas($conexion, $ventas_id);
            if (!empty($venta)) {
                http_response_code(200); // OK
                header('Content-Type: application/json'); // Establecer el tipo de contenido JSON
                echo json_encode($venta, JSON_PRETTY_PRINT);
            } else {
                http_response_code(404); // No encontrado
                header('Content-Type: application/json');
                echo json_encode(array("error" => "Venta no encontrada"), JSON_PRETTY_PRINT);
            }
        } else {
            // Obtener todas las ventas si no se proporciona un ID
            $ventas = obtenerVentas($conexion);
            header('Content-Type: application/json'); // Establecer el tipo de contenido JSON
            echo json_encode($ventas, JSON_PRETTY_PRINT);
        }
        break;
    case 'PUT':
        // Actualizar una venta
        $ventas_id = isset($_GET['ventas_id']) ? $_GET['ventas_id'] : null;
        $datos = json_decode(file_get_contents("php://input"), true);
        if (actualizarVenta($ventas_id, $datos, $conexion)) {
            http_response_code(200); // OK
        } else {
            http_response_code(500); // Error del servidor
        }
        break;
    case 'DELETE':
        // Eliminar una venta
        $ventas_id = isset($_GET['ventas_id']) ? $_GET['ventas_id'] : null;
        if (eliminarVenta($ventas_id, $conexion)) {
            http_response_code(200); // OK
        } else {
            http_response_code(500); // Error del servidor
        }
        break;
    default:
        // Método no permitido
        http_response_code(405); // Método no permitido
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Método no permitido"), JSON_PRETTY_PRINT);
        break;
}

?>