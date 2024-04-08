<?php

require_once("../config/conexion.php");

// Función para crear un nuevo detalle
function crearDetalle($datosDetalle, $conexion) {
    // Verificar si los datos están en formato JSON
    if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
        http_response_code(400); // Bad Request
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Los datos deben enviarse en formato JSON"), JSON_PRETTY_PRINT);
        return false;
    }

    $usuario_id = $datosDetalle['usuario_id'] ?? '';
    $productos_id = $datosDetalle['productos_id'] ?? '';
    $ventas_id = $datosDetalle['ventas_id'] ?? '';
    $precio_venta = $datosDetalle['precio_venta'] ?? '';
    $cantidad = $datosDetalle['cantidad'] ?? '';
    $fecha_venta = $datosDetalle['fecha_venta'] ?? date('Y-m-d H:i:s');
    $fecha_actualizacion = date('Y-m-d H:i:s');

    // Verificar si todos los campos requeridos están presentes
    if (empty($usuario_id) || empty($productos_id) || empty($ventas_id) || empty($precio_venta) || empty($cantidad)) {
        http_response_code(400); // Bad Request
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Todos los campos son requeridos"), JSON_PRETTY_PRINT);
        return false;
    }

    // Insertar el nuevo detalle en la base de datos
    $sql = "INSERT INTO detalles (usuario_id, productos_id, ventas_id, precio_venta, cantidad, fecha_venta, fecha_actualizacion) 
            VALUES ($usuario_id, $productos_id, $ventas_id, $precio_venta, $cantidad, '$fecha_venta', '$fecha_actualizacion')";

    if ($conexion->query($sql) === TRUE) {
        http_response_code(201); // Creado
        header('Content-Type: application/json');
        echo json_encode(array("mensaje" => "Detalle creado correctamente"), JSON_PRETTY_PRINT);
        return true;
    } else {
        http_response_code(500); // Error del servidor
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Error al crear el detalle: " . $conexion->error), JSON_PRETTY_PRINT);
        return false;
    }
}

// Función para obtener detalles
function obtenerDetalles($conexion, $detalles_id = null) {
    $detalles = array();

    if ($detalles_id !== null) {
        // Obtener un detalle por su ID
        $sql = "SELECT * FROM detalles WHERE detalles_id = $detalles_id";
        $resultado = $conexion->query($sql);

        if ($resultado->num_rows > 0) {
            $detalles[] = $resultado->fetch_assoc(); // Aquí se empaqueta dentro de un array
        }
    } else {
        // Obtener todos los detalles
        $sql = "SELECT * FROM detalles";
        $resultado = $conexion->query($sql);

        if ($resultado->num_rows > 0) {
            while($fila = $resultado->fetch_assoc()) {
                $detalles[] = $fila;
            }
        }
    }

    return $detalles;
}

// Función para actualizar un detalle existente
function actualizarDetalle($detalles_id, $datosDetalle, $conexion) {
    // Verificar si el ID está definido
    if (!isset($detalles_id)) {
        http_response_code(400); // Bad Request
        header('Content-Type: application/json');
        echo json_encode(array("error" => "ID de detalle no proporcionado"), JSON_PRETTY_PRINT);
        return false;
    }

    $usuario_id = $datosDetalle['usuario_id'] ?? '';
    $productos_id = $datosDetalle['productos_id'] ?? '';
    $ventas_id = $datosDetalle['ventas_id'] ?? '';
    $precio_venta = $datosDetalle['precio_venta'] ?? '';
    $cantidad = $datosDetalle['cantidad'] ?? '';
    $fecha_venta = $datosDetalle['fecha_venta'] ?? '';
    $fecha_actualizacion = date('Y-m-d H:i:s');

    // Verificar si todos los campos requeridos están presentes
    if (empty($usuario_id) || empty($productos_id) || empty($ventas_id) || empty($precio_venta) || empty($cantidad)) {
        http_response_code(400); // Bad Request
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Todos los campos son requeridos"), JSON_PRETTY_PRINT);
        return false;
    }

    // Verificar si el detalle con el ID proporcionado existe
    $detalle_existente = obtenerDetalles($conexion, $detalles_id);
    if (empty($detalle_existente)) {
        http_response_code(404); // Not Found
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Detalle no encontrado en la base de datos"), JSON_PRETTY_PRINT);
        return false;
    }

    $sql = "UPDATE detalles SET usuario_id=$usuario_id, productos_id=$productos_id, ventas_id=$ventas_id, precio_venta=$precio_venta, cantidad=$cantidad, fecha_venta='$fecha_venta', fecha_actualizacion='$fecha_actualizacion' WHERE detalles_id=$detalles_id";

    if ($conexion->query($sql) === TRUE) {
        header('Content-Type: application/json');
        echo json_encode(array("mensaje" => "Detalle actualizado correctamente"), JSON_PRETTY_PRINT);
        return true;
    } else {
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Error al actualizar el detalle: " . $conexion->error), JSON_PRETTY_PRINT);
        return false;
    }
}

// Función para eliminar un detalle
function eliminarDetalle($detalles_id, $conexion) {
    // Verificar si el ID está definido
    if (!isset($detalles_id)) {
        http_response_code(400); // Bad Request
        header('Content-Type: application/json');
        echo json_encode(array("error" => "ID de detalle no proporcionado"), JSON_PRETTY_PRINT);
        return false;
    }

    $sql = "DELETE FROM detalles WHERE detalles_id=$detalles_id";

    if ($conexion->query($sql) === TRUE) {
        header('Content-Type: application/json');
        echo json_encode(array("mensaje" => "Detalle eliminado correctamente"), JSON_PRETTY_PRINT);
        return true;
    } else {
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Error al eliminar el detalle: " . $conexion->error), JSON_PRETTY_PRINT);
        return false;
    }
}

// Manejo de las solicitudes HTTP

// Obtener el método de la solicitud HTTP
$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'POST':
        // Crear un nuevo detalle
        $datos = json_decode(file_get_contents("php://input"), true);
        if (crearDetalle($datos, $conexion)) {
            http_response_code(201); // Creado
        } else {
            http_response_code(500); // Error del servidor
        }
        break;
    case 'GET':
        // Obtener un detalle por su ID si se proporciona
        if (isset($_GET['detalles_id'])) {
            $detalles_id = $_GET['detalles_id'];
            $detalle = obtenerDetalles($conexion, $detalles_id);
            if (!empty($detalle)) {
                http_response_code(200); // OK
                header('Content-Type: application/json'); // Establecer el tipo de contenido JSON
                echo json_encode($detalle, JSON_PRETTY_PRINT);
            } else {
                http_response_code(404); // No encontrado
                header('Content-Type: application/json');
                echo json_encode(array("error" => "Detalle no encontrado"), JSON_PRETTY_PRINT);
            }
        } else {
            // Obtener todos los detalles si no se proporciona un ID
            $detalles = obtenerDetalles($conexion);
            header('Content-Type: application/json'); // Establecer el tipo de contenido JSON
            echo json_encode($detalles, JSON_PRETTY_PRINT);
        }
        break;
    case 'PUT':
        // Actualizar un detalle
        $detalles_id = isset($_GET['detalles_id']) ? $_GET['detalles_id'] : null;
        $datos = json_decode(file_get_contents("php://input"), true);
        if (actualizarDetalle($detalles_id, $datos, $conexion)) {
            http_response_code(200); // OK
        } else {
            http_response_code(500); // Error del servidor
        }
        break;
    case 'DELETE':
        // Eliminar un detalle
        $detalles_id = isset($_GET['detalles_id']) ? $_GET['detalles_id'] : null;
        if (eliminarDetalle($detalles_id, $conexion)) {
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
