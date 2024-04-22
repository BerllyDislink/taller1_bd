<?php
require_once '../../Models/sales_model/sales_consulta.php';
require_once '../../response_formatters/Formatter.php';
require_once '../../view/view.php';

$formato = new Formatter();

// Verifica si se ha proporcionado un ID de venta
$ventas_id = isset($_GET['ventas_id']) ? $_GET['ventas_id'] : null;

// Verifica si los datos están vacíos o no se proporcionaron
$datos = json_decode(file_get_contents("php://input"), true);

if (empty($datos) || !$ventas_id) {
    mostrarRespuesta(400, ['mensaje' => 'ID de venta y datos de actualización son necesarios'], $formato);
    exit();
}

// Lógica para actualizar una venta existente
$exito = actualizarVenta($conexion, $ventas_id, $datos);

if ($exito) {
    mostrarRespuesta(200, ['mensaje' => 'Venta actualizada correctamente'], $formato);
} else {
    mostrarRespuesta(500, ['mensaje' => 'Error al actualizar la venta'], $formato);
}
