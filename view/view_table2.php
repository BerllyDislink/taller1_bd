<?php

// Función para mostrar la respuesta JSON
function mostrarRespuestaJSON($codigo, $datos) {
    header('Content-Type: application/json');
    http_response_code($codigo);
    echo json_encode($datos, JSON_PRETTY_PRINT);
}

// Función para mostrar una respuesta JSON de error
function mostrarErrorJSON($codigo, $mensaje) {
    mostrarRespuestaJSON($codigo, ["error" => $mensaje]);
}

?>