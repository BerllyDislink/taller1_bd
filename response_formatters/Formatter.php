<?php
// response_formatters/JsonFormatter.php
class Formatter {
    // Esta clase representa el formateador base con métodos que pueden ser sobreescritos por subclases

    public function format($datos) {
        // Método base para formatear datos, por defecto, es JSON
        return json_encode($datos, JSON_PRETTY_PRINT);
    }

    public function getContentType() {
        // Tipo de contenido base, por defecto, es JSON
        return 'application/json';
    }
}

// Clase para formatear datos en formato JSON
class JsonFormatter extends Formatter {
    public function format($datos) {
        // Formatea los datos en JSON
        return json_encode($datos, JSON_PRETTY_PRINT);
    }

    public function getContentType() {
        // Tipo de contenido para JSON
        return 'application/json';
    }
}




