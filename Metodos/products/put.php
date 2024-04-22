
<?php
require_once '../../Models/product_model/product_consulta.php';
require_once '../../response_formatters/Formatter.php';
require_once '../../view/view.php';

// Crea una instancia de Formatter para formatear la respuesta en formato JSON
$formato = new Formatter();

// Verifica si se ha proporcionado un ID de producto
$productos_id = isset($_GET['productos_id']) ? $_GET['productos_id'] : null;

// Verifica si los datos están vacíos o no se proporcionaron
$datos = json_decode(file_get_contents("php://input"), true);

if (empty($datos) || !$productos_id) {
    mostrarRespuesta(400, ['mensaje' => 'ID de producto y datos de actualizacion son necesarios'], $formato);
    exit();
}

// Lógica para actualizar un producto existente
$exito = actualizarProducto($conexion, $productos_id, $datos);

if ($exito) {
    mostrarRespuesta(200, ['mensaje' => 'Producto actualizado correctamente'], $formato);
} else {
    mostrarRespuesta(500, ['mensaje' => 'Error al actualizar el producto'], $formato);
}
