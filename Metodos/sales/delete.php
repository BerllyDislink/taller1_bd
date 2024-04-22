<?php
require_once '../../Models/sales_model/sales_consulta.php';
require_once '../../response_formatters/Formatter.php';
require_once '../../view/view.php';

$formato = new Formatter();

// Verifica si se ha proporcionado un ID de venta
$ventas_id = isset($_GET['ventas_id']) ? $_GET['ventas_id'] : null;

if (!$ventas_id) {
    mostrarRespuesta(400, ['mensaje' => 'ID de venta es necesario para eliminar'], $formato);
    exit();
}

// Lógica para eliminar una venta
$exito = eliminarVenta($ventas_id, $conexion);

if ($exito) {
    mostrarRespuesta(200, ['mensaje' => 'Venta eliminada correctamente'], $formato);
} else {
    mostrarRespuesta(500, ['mensaje' => 'Error al eliminar la venta'], $formato);
}
