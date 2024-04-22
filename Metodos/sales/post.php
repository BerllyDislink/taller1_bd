<?php
require_once '../../Models/sales_model/sales_consulta.php';
require_once '../../response_formatters/Formatter.php';
require_once '../../view/view.php';

$formato = new Formatter();

// Obtén los datos de la solicitud `POST`
$datos = json_decode(file_get_contents("php://input"), true);

// Verifica si no se proporcionaron datos
if (empty($datos)) {
    // Si no se proporcionan datos, devuelve un error
    mostrarRespuesta(400, ['mensaje' => 'Los datos son necesarios para crear una venta'], $formato);
    exit();
}

// Lógica para crear una nueva venta
$exito = crearVenta($datos, $conexion);

if ($exito) {
    mostrarRespuesta(201, ['mensaje' => 'Venta creada correctamente'], $formato);
} else {
    mostrarRespuesta(500, ['mensaje' => 'Error al crear la venta'], $formato);
}
