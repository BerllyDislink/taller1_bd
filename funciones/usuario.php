<?php

require_once ("../config/conexion.php");

// Función para crear un nuevo usuario
function crearUsuario($datosUsuario, $conexion)
{
    // Verificar si los datos están en formato JSON
    if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
        http_response_code(400); // Bad Request
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Los datos deben enviarse en formato JSON"), JSON_PRETTY_PRINT);
        return false;
    }

    $nombre = $datosUsuario['nombre'] ?? '';
    $edad = $datosUsuario['edad'] ?? '';
    $correo = $datosUsuario['correo'] ?? '';
    $fecha_registro = $datosUsuario['fecha_registro'] ?? '';

    // Verificar si todos los campos requeridos están presentes
    if (empty($nombre) || empty($edad) || empty($correo) || empty($fecha_registro)) {
        http_response_code(400); // Bad Request
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Todos los campos son requeridos"), JSON_PRETTY_PRINT);
        return false;
    }

    // Insertar el nuevo usuario en la base de datos
    $sql = "INSERT INTO usuarios (nombre, edad, correo, fecha_registro) VALUES ('$nombre', $edad, '$correo', '$fecha_registro')";

    if ($conexion->query($sql) === TRUE) {
        http_response_code(201); // Creado
        header('Content-Type: application/json');
        echo json_encode(array("mensaje" => "Usuario creado correctamente"), JSON_PRETTY_PRINT);
        return true;
    } else {
        http_response_code(500); // Error del servidor
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Error al crear el usuario"), JSON_PRETTY_PRINT);
        return false;
    }
}

// Función para obtener usuarios
function obtenerUsuarios($conexion, $usuario_id = null)
{
    $usuarios = array();

    if ($usuario_id !== null) {
        // Obtener un usuario por su ID
        $sql = "SELECT * FROM usuarios WHERE usuario_id = $usuario_id";
        $resultado = $conexion->query($sql);

        if ($resultado->num_rows > 0) {
            $usuarios[] = $resultado->fetch_assoc(); // Aquí se empaqueta dentro de un array
        }
    } else {
        // Obtener todos los usuarios
        $sql = "SELECT * FROM usuarios";
        $resultado = $conexion->query($sql);

        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $usuarios[] = $fila;
            }
        }
    }

    return $usuarios;
}

// Función para actualizar un usuario existente
function actualizarUsuario($usuario_id, $datosUsuario, $conexion)
{
    // Verificar si los datos están en formato JSON
    if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
        http_response_code(400); // Bad Request
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Solo se acepta formato JSON"), JSON_PRETTY_PRINT);
        return false;
    }

    // Verificar si el ID está definido
    if (!isset($usuario_id)) {
        http_response_code(400); // Bad Request
        header('Content-Type: application/json');
        echo json_encode(array("error" => "ID de usuario no proporcionado"), JSON_PRETTY_PRINT);
        return false;
    }

    $nombre = $datosUsuario['nombre'] ?? '';
    $edad = $datosUsuario['edad'] ?? '';

    // Verificar si todos los campos requeridos están presentes
    if (empty($nombre) || empty($edad)) {
        http_response_code(400); // Bad Request
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Todos los campos son requeridos"), JSON_PRETTY_PRINT);
        return false;
    }

    // Verificar si el usuario con el ID proporcionado existe
    $usuario_existente = obtenerUsuarios($conexion, $usuario_id);
    if (empty($usuario_existente)) {
        http_response_code(404); // Not Found
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Usuario no encontrado en la base de datos"), JSON_PRETTY_PRINT);
        return false;
    }

    $sql = "UPDATE usuarios SET nombre='$nombre', edad=$edad WHERE usuario_id=$usuario_id";

    if ($conexion->query($sql) === TRUE) {
        header('Content-Type: application/json');
        echo json_encode(array("mensaje" => "Usuario actualizado correctamente"), JSON_PRETTY_PRINT);
        return true;
    } else {
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Error al actualizar el usuario: " . $conexion->error), JSON_PRETTY_PRINT);
        return false;
    }
}

// Función para eliminar un usuario
function eliminarUsuario($usuario_id, $conexion)
{
    $sql = "DELETE FROM usuarios WHERE usuario_id=$usuario_id";

    if ($conexion->query($sql) === TRUE) {
        header('Content-Type: application/json');
        echo json_encode(array("mensaje" => "Usuario eliminado correctamente"), JSON_PRETTY_PRINT);
        return true;
    } else {
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Error al eliminar el usuario"), JSON_PRETTY_PRINT);
        return false;
    }
}

// Manejo de las solicitudes HTTP

// Obtener el método de la solicitud HTTP
$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'POST':
        // Crear un nuevo usuario
        $datos = json_decode(file_get_contents("php://input"), true);
        if (crearUsuario($datos, $conexion)) {
            http_response_code(201); // Creado
        } else {
            http_response_code(500); // Error del servidor
        }
        break;
    case 'GET':
        // Obtener un usuario por su ID si se proporciona
        if (isset($_GET['usuario_id'])) {
            $usuario_id = $_GET['usuario_id'];
            $usuario = obtenerUsuarios($conexion, $usuario_id);
            if (!empty($usuario)) {
                http_response_code(200); // OK
                header('Content-Type: application/json'); // Establecer el tipo de contenido JSON
                echo json_encode($usuario, JSON_PRETTY_PRINT);
            } else {
                http_response_code(404); // No encontrado
                header('Content-Type: application/json');
                echo json_encode(array("error" => "Usuario no encontrado"), JSON_PRETTY_PRINT);
            }
        } else {
            // Obtener todos los usuarios si no se proporciona un ID
            $usuarios = obtenerUsuarios($conexion);
            header('Content-Type: application/json'); // Establecer el tipo de contenido JSON
            echo json_encode($usuarios, JSON_PRETTY_PRINT);
        }
        break;
    case 'PUT':
        // Actualizar un usuario
        $usuario_id = isset($_GET['usuario_id']) ? $_GET['usuario_id'] : null;
        $datos = json_decode(file_get_contents("php://input"), true);
        if (actualizarUsuario($usuario_id, $datos, $conexion)) {
            http_response_code(200); // OK
        } else {
            http_response_code(500); // Error del servidor
        }
        break;
    case 'DELETE':
        // Eliminar un usuario
        $usuario_id = $_GET['usuario_id'];
        if (eliminarUsuario($usuario_id, $conexion)) {
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