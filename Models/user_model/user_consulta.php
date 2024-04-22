<?php
require_once("../../config/conexion.php");

// Función para crear un nuevo usuario
function crearUsuario($datosUsuario, $conexion) {
    // Verificar si los datos están en formato JSON
    if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
        http_response_code(400); // Bad Request
        echo json_encode(array("error" => "Los datos deben enviarse en formato JSON"), JSON_PRETTY_PRINT);
        return false;
    }

    // Desestructurar los datos del usuario
    $nombre = $datosUsuario['nombre'] ?? '';
    $edad = $datosUsuario['edad'] ?? 0;
    $correo = $datosUsuario['correo'] ?? '';
    $fecha_registro = date('Y-m-d'); // Obtener la fecha actual

    // Verificar si todos los campos requeridos están presentes
    if (empty($nombre) || empty($correo) || $edad <= 0) {
        http_response_code(400); // Bad Request
        echo json_encode(array("error" => "Todos los campos son requeridos"), JSON_PRETTY_PRINT);
        return false;
    }

    // Insertar el nuevo usuario en la base de datos
    $sql = "INSERT INTO usuarios (nombre, edad, correo, fecha_registro) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("siss", $nombre, $edad, $correo, $fecha_registro);

    if ($stmt->execute()) {
        http_response_code(201); // Creado
        return true;
    } else {
        http_response_code(500); // Error del servidor
        echo json_encode(array("error" => "Error al crear el usuario"), JSON_PRETTY_PRINT);
        return false;
    }
}

// Función para obtener un usuario por su ID
function obtenerUsuarioPorId($conexion, $usuario_id) {
    $sql = "SELECT * FROM usuarios WHERE usuario_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();
    $stmt->close();
    return $usuario;
}

// Función para obtener todos los usuarios
function obtenerUsuarios($conexion) {
    $sql = "SELECT * FROM usuarios";
    $resultado = $conexion->query($sql);
    $usuarios = $resultado->fetch_all(MYSQLI_ASSOC);
    $resultado->close();
    return $usuarios;
}

// Función para actualizar un usuario existente
function actualizarUsuario($conexion, $usuario_id, $datosUsuario) {
    // Verificar si los datos están en formato JSON
    if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
        http_response_code(400);
        echo json_encode(array("error" => "Los datos deben enviarse en formato JSON"), JSON_PRETTY_PRINT);
        return false;
    }

    // Verificar si el ID está definido
    if (empty($usuario_id)) {
        http_response_code(400);
        echo json_encode(array("error" => "ID de usuario no proporcionado"), JSON_PRETTY_PRINT);
        return false;
    }

    // Desestructurar los datos del usuario
    $nombre = $datosUsuario['nombre'] ?? '';
    $edad = $datosUsuario['edad'] ?? 0;

    // Verificar si todos los campos requeridos están presentes
    if (empty($nombre) || $edad <= 0) {
        http_response_code(400);
        echo json_encode(array("error" => "Todos los campos son requeridos"), JSON_PRETTY_PRINT);
        return false;
    }

    // Prepara la consulta SQL para actualizar el usuario
    $sql = "UPDATE usuarios SET nombre = ?, edad = ? WHERE usuario_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sii", $nombre, $edad, $usuario_id);

    // Ejecuta la consulta preparada
    if ($stmt->execute()) {
        http_response_code(200);
        return true;
    } else {
        http_response_code(500);
        echo json_encode(array("error" => "Error al actualizar el usuario"), JSON_PRETTY_PRINT);
        return false;
    }
}

// Función para eliminar un usuario existente
function eliminarUsuario($usuario_id, $conexion) {
    // Verifica si el ID de usuario está definido
    if (!isset($usuario_id)) {
        http_response_code(400);
        echo json_encode(array("error" => "ID de usuario no proporcionado"), JSON_PRETTY_PRINT);
        return false;
    }
    
    // Prepara la consulta SQL para eliminar el usuario
    $sql = "DELETE FROM usuarios WHERE usuario_id = ?";
    $stmt = $conexion->prepare($sql);
    
    // Verifica si la preparación de la consulta fue exitosa
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(array("error" => "Error al preparar la consulta"), JSON_PRETTY_PRINT);
        return false;
    }
    
    // Asocia el ID de usuario a la consulta preparada
    $stmt->bind_param("i", $usuario_id);
    
    // Ejecuta la consulta preparada
    if ($stmt->execute()) {
        // Usuario eliminado correctamente
        return true;
    } else {
        // Error al eliminar el usuario
        return false;
    }
}


