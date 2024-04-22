<?php

require_once("../../config/conexion.php");

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
        return true;
    } else {
        http_response_code(500); // Error del servidor
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
function actualizarProducto($conexion, $productos_id, $datosProducto) {
    // Verifica si el ID está definido
    if (!isset($productos_id)) {
        http_response_code(400); // Bad Request
        echo json_encode(["error" => "ID de producto no proporcionado"], JSON_PRETTY_PRINT);
        return false;
    }

    // Desestructura los datos del producto
    $nombre = $datosProducto['nombre'] ?? '';
    $precio = $datosProducto['precio'] ?? '';
    $descripcion = $datosProducto['descripcion'] ?? '';
    $cantidad_productos = $datosProducto['cantidad_productos'] ?? 0;

    // Verifica si todos los campos requeridos están presentes
    if (empty($nombre) || empty($precio) || empty($descripcion)) {
        http_response_code(400); // Bad Request
        echo json_encode(["error" => "Todos los campos son requeridos"], JSON_PRETTY_PRINT);
        return false;
    }

    // Verifica si el producto con el ID proporcionado existe
    $sql = "SELECT 1 FROM productos WHERE productos_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $productos_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        http_response_code(404); // Not Found
        echo json_encode(["error" => "Producto no encontrado en la base de datos"], JSON_PRETTY_PRINT);
        return false;
    }
    $stmt->close();

    // Actualiza el producto en la base de datos
    $sql = "UPDATE productos SET nombre = ?, precio = ?, descripcion = ?, cantidad_productos = ? WHERE productos_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sdssi", $nombre, $precio, $descripcion, $cantidad_productos, $productos_id);

    if ($stmt->execute()) {
        http_response_code(200); // OK
        return true;
    } else {
        http_response_code(500); // Error del servidor
        return false;
    }
}




// Función para eliminar un producto
function eliminarProducto($productos_id, $conexion) {
    // Verificar si el ID está definido
    if (!isset($productos_id)) {
        http_response_code(400); // Bad Request
        return false;
    }

    $sql = "DELETE FROM productos WHERE productos_id=$productos_id";

    if ($conexion->query($sql) === TRUE) {

        return true;
    } else {

        return false;
    }
}