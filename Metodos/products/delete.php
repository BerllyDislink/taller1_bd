<?php
require_once '../../Models/product_model/product_consulta.php';
require_once '../../response_formatters/Formatter.php';
require_once '../../view/view.php';

// Crea una instancia de Formatter para formatear la respuesta en formato JSON
$formato = new Formatter();

// Verifica si se ha proporcionado un ID de producto
$productos_id = isset($_GET['productos_id']) ? $_GET['productos_id'] : null;

if (!$productos_id) {
    mostrarRespuesta(400, ['mensaje' => 'ID de producto es necesario para eliminar'], $formato);
    exit();
}

// Lógica para eliminar un producto
$exito = eliminarProducto($productos_id, $conexion);

if ($exito) {
    mostrarRespuesta(200, ['mensaje' => 'Producto eliminado correctamente'], $formato);
} else {
    mostrarRespuesta(500, ['mensaje' => 'Error al eliminar el producto'], $formato);
}
