<?php
// response_formatters/JsonFormatter.php
class JsonFormatter {
    public function format($datos) {
        return json_encode($datos, JSON_PRETTY_PRINT);
    }

    public function getContentType() {
        return 'application/json';
    }
}
