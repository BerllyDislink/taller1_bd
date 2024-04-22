<?php
// Incluye el archivo Formatter.php que contiene todas las clases de formateo (JSON, HTML, XML)


// Función para mostrar la respuesta formateada al cliente
function mostrarRespuesta($codigo, $datos, $formato) {
    // Establece el código de respuesta HTTP
    http_response_code($codigo);
    
    // Establece el tipo de contenido de la respuesta utilizando el formateador
    header('Content-Type: ' . $formato->getContentType());
    
    // Formatea los datos utilizando el método `format` del formateador
    echo $formato->format($datos);
}