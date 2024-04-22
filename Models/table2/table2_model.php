<?php
require_once("../../config/conexion.php");
// Función para obtener los datos según el ID proporcionado
function obtenerDatos($detalles_id, $conexion) {
    // Preparar la consulta SQL con una sentencia parametrizada
    $sql = "SELECT 
                t1.usuario_id, t1.nombre AS nombre_usuario, t1.correo,
                t2.productos_id, t2.nombre AS nombre_producto, t2.precio,
                t3.ventas_id, t3.precio AS precio_venta, t3.cantidad_venta,
                t4.detalles_id, t4.cantidad, t4.fecha_actualizacion
            FROM 
                usuarios t1
                JOIN detalles t4 ON t1.usuario_id = t4.usuario_id
                JOIN productos t2 ON t4.productos_id = t2.productos_id
                JOIN ventas t3 ON t4.ventas_id = t3.ventas_id
            WHERE 
                t4.detalles_id = ? 
            LIMIT 1";

    // Preparar la sentencia
    $stmt = $conexion->prepare($sql);

    // Asociar el parámetro
    $stmt->bind_param("i", $detalles_id);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener el resultado
    $resultado = $stmt->get_result();

    // Verificar si se obtuvieron resultados
    if ($resultado->num_rows > 0) {
        // Obtener el primer (y único) registro
        $fila = $resultado->fetch_assoc();
        return $fila;
    } else {
        return null; // No se encontraron resultados
    }

    // Cerrar la sentencia
    $stmt->close();
}

?>
