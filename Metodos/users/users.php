<?php
$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'POST':
        // Llama al archivo que maneja las solicitudes POST
        require_once 'post.php';
        break;
    case 'GET':
        // Llama al archivo que maneja las solicitudes GET
        require_once 'get.php';
        break;
    case 'PUT':
        // Llama al archivo que maneja las solicitudes PUT
        require_once 'put.php';
        break;
    case 'DELETE':
        // Llama al archivo que maneja las solicitudes DELETE
        require_once 'delete.php';
        break;
    default:
        // Método no permitido
        http_response_code(405);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Método no permitido'], JSON_PRETTY_PRINT);
        break;
}
