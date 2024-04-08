<?php

require_once("../config/conexion.php");

// Función para crear un nuevo producto
function crearProducto($datosProducto, $conexion) {
    // Verificar si los datos están en formato JSON
    if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
        http_response_code(400); // Bad Request
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Los datos deben enviarse en formato JSON"), JSON_PRETTY_PRINT);
        return false;
    }

    $nombre = $datosProducto['nombre'] ?? '';
    $precio = $datosProducto['precio'] ?? '';
    $descripcion = $datosProducto['descripcion'] ?? '';
    $fecha_creacion = date('Y-m-d'); // Obtener la fecha actual
    $cantidad_productos = $datosProducto['cantidad_productos'] ?? 0;

    // Verificar si todos los campos requeridos están presentes
    if (empty($nombre) || empty($precio) || empty($descripcion)) {
        http_response_code(400); // Bad Request
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Todos los campos son requeridos"), JSON_PRETTY_PRINT);
        return false;
    }

    // Insertar el nuevo producto en la base de datos
    $sql = "INSERT INTO productos (nombre, precio, descripcion, fecha_creacion, cantidad_productos) 
            VALUES ('$nombre', $precio, '$descripcion', '$fecha_creacion', $cantidad_productos)";

    if ($conexion->query($sql) === TRUE) {
        http_response_code(201); // Creado
        header('Content-Type: application/json');
        echo json_encode(array("mensaje" => "Producto creado correctamente"), JSON_PRETTY_PRINT);
        return true;
    } else {
        http_response_code(500); // Error del servidor
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Error al crear el producto"), JSON_PRETTY_PRINT);
        return false;
    }
}

// Función para obtener productos
function obtenerProductos($conexion, $productos_id = null) {
    $productos = array();

    if ($productos_id !== null) {
        // Obtener un producto por su ID
        $sql = "SELECT * FROM productos WHERE productos_id = $productos_id";
        $resultado = $conexion->query($sql);

        if ($resultado->num_rows > 0) {
            $productos[] = $resultado->fetch_assoc(); // Aquí se empaqueta dentro de un array
        }
    } else {
        // Obtener todos los productos
        $sql = "SELECT * FROM productos";
        $resultado = $conexion->query($sql);

        if ($resultado->num_rows > 0) {
            while($fila = $resultado->fetch_assoc()) {
                $productos[] = $fila;
            }
        }
    }

    return $productos;
}

// Función para actualizar un producto existente
function actualizarProducto($productos_id, $datosProducto, $conexion) {
    // Verificar si el ID está definido
    if (!isset($productos_id)) {
        http_response_code(400); // Bad Request
        header('Content-Type: application/json');
        echo json_encode(array("error" => "ID de producto no proporcionado"), JSON_PRETTY_PRINT);
        return false;
    }

    $nombre = $datosProducto['nombre'] ?? '';
    $precio = $datosProducto['precio'] ?? '';
    $descripcion = $datosProducto['descripcion'] ?? '';
    $cantidad_productos = $datosProducto['cantidad_productos'] ?? 0;

    // Verificar si todos los campos requeridos están presentes
    if (empty($nombre) || empty($precio) || empty($descripcion)) {
        http_response_code(400); // Bad Request
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Todos los campos son requeridos"), JSON_PRETTY_PRINT);
        return false;
    }

    // Verificar si el producto con el ID proporcionado existe
    $producto_existente = obtenerProductos($conexion, $productos_id);
    if (empty($producto_existente)) {
        http_response_code(404); // Not Found
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Producto no encontrado en la base de datos"), JSON_PRETTY_PRINT);
        return false;
    }

    $sql = "UPDATE productos SET nombre='$nombre', precio=$precio, descripcion='$descripcion', cantidad_productos=$cantidad_productos WHERE productos_id=$productos_id";

    if ($conexion->query($sql) === TRUE) {
        header('Content-Type: application/json');
        echo json_encode(array("mensaje" => "Producto actualizado correctamente"), JSON_PRETTY_PRINT);
        return true;
    } else {
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Error al actualizar el producto"), JSON_PRETTY_PRINT);
        return false;
    }
}


// Función para eliminar un producto
function eliminarProducto($productos_id, $conexion) {
    // Verificar si el ID está definido
    if (!isset($productos_id)) {
        http_response_code(400); // Bad Request
        header('Content-Type: application/json');
        echo json_encode(array("error" => "ID de producto no proporcionado"), JSON_PRETTY_PRINT);
        return false;
    }

    $sql = "DELETE FROM productos WHERE productos_id=$productos_id";

    if ($conexion->query($sql) === TRUE) {
        header('Content-Type: application/json');
        echo json_encode(array("mensaje" => "Producto eliminado correctamente"), JSON_PRETTY_PRINT);
        return true;
    } else {
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Error al eliminar el producto"), JSON_PRETTY_PRINT);
        return false;
    }
}

// Manejo de las solicitudes HTTP

// Obtener el método de la solicitud HTTP
$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'POST':
        // Crear un nuevo producto
        $datos = json_decode(file_get_contents("php://input"), true);
        if (crearProducto($datos, $conexion)) {
            http_response_code(201); // Creado
        } else {
            http_response_code(500); // Error del servidor
        }
        break;
    case 'GET':
        // Obtener un producto por su ID si se proporciona
        if (isset($_GET['productos_id'])) {
            $productos_id = $_GET['productos_id'];
            $producto = obtenerProductos($conexion, $productos_id);
            if (!empty($producto)) {
                http_response_code(200); // OK
                header('Content-Type: application/json'); // Establecer el tipo de contenido JSON
                echo json_encode($producto, JSON_PRETTY_PRINT);
            } else {
                http_response_code(404); // No encontrado
                header('Content-Type: application/json');
                echo json_encode(array("error" => "Producto no encontrado"), JSON_PRETTY_PRINT);
            }
        } else {
            // Obtener todos los productos si no se proporciona un ID
            $productos = obtenerProductos($conexion);
            header('Content-Type: application/json'); // Establecer el tipo de contenido JSON
            echo json_encode($productos, JSON_PRETTY_PRINT);
        }
        break;
    case 'PUT':
        // Actualizar un producto
        $productos_id = isset($_GET['productos_id']) ? $_GET['productos_id'] : null;
        $datos = json_decode(file_get_contents("php://input"), true);
        if (actualizarProducto($productos_id, $datos, $conexion)) {
            http_response_code(200); // OK
        } else {
            http_response_code(500); // Error del servidor
        }
        break;
    case 'DELETE':
        // Eliminar un producto
        $productos_id = isset($_GET['productos_id']) ? $_GET['productos_id'] : null;
        if (eliminarProducto($productos_id, $conexion)) {
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
