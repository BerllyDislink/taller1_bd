<?php
require_once '../../Models/product_model/product_consulta.php';
require_once '../../response_formatters/Formatter.php';
require_once '../../view/view.php';

// Crea una instancia de Formatter para formatear la respuesta en formato JSON
$formato = new Formatter();

// Obtén los datos de la solicitud `POST`
$datos = json_decode(file_get_contents("php://input"), true);

// Verifica si no se proporcionaron datos
if (empty($datos)) {
    // Si no se proporcionan datos, devuelve un error
    mostrarRespuesta(400, ['mensaje' => 'Los datos son necesarios para crear un producto'], $formato);
    exit; // Finaliza la ejecución del script
}

// Lógica para crear un nuevo producto
$exito = crearProducto($datos, $conexion);
if ($exito) {
    mostrarRespuesta(201, ['mensaje' => 'Producto creado correctamente'], $formato);
} else {
    mostrarRespuesta(500, ['mensaje' => 'Error al crear el producto'], $formato);
}
