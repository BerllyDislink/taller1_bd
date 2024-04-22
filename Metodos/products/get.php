<?php
require_once '../../Models/product_model/product_consulta.php';
require_once '../../response_formatters/Formatter.php';
require_once '../../view/view.php';

$formato = new Formatter();

if (isset($_GET['productos_id'])) {
    $productos_id = $_GET['productos_id'];
    $producto = obtenerProductos($conexion, $productos_id);

    if (!empty($producto)) {
        mostrarRespuesta(200, $producto, $formato);
    } else {
        mostrarRespuesta(404, ['mensaje' => 'Producto no encontrado'], $formato);
    }
} else {
    $productos = obtenerProductos($conexion);
    mostrarRespuesta(200, $productos, $formato);
}
