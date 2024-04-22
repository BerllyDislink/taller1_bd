<?php
require_once '../../Models/sales_model/sales_consulta.php';
require_once '../../response_formatters/Formatter.php';
require_once '../../view/view.php';

$formato = new Formatter();

if (isset($_GET['ventas_id'])) {
    $ventas_id = $_GET['ventas_id'];
    $venta = obtenerVentas($conexion, $ventas_id);

    if (!empty($venta)) {
        mostrarRespuesta(200, $venta, $formato);
    } else {
        mostrarRespuesta(404, ['mensaje' => 'Venta no encontrada'], $formato);
    }
} else {
    $ventas = obtenerVentas($conexion);
    mostrarRespuesta(200, $ventas, $formato);
}
