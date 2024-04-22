<?php
 require_once ("../../config/conexion.php");
// Función para obtener datos según el ID proporcionado de la base de datos
function obtenerDatos($productos_id, $conexion) {
    // Preparar la consulta SQL con una sentencia parametrizada
    $sql = "SELECT 
                d.detalles_id, d.cantidad, d.fecha_actualizacion,
                p.productos_id, p.nombre AS nombre_producto, p.precio
            FROM 
                detalles d
                JOIN productos p ON d.productos_id = p.productos_id
            WHERE 
                d.productos_id = ?";

    // Preparar la sentencia
    $stmt = $conexion->prepare($sql);

    // Asociar el parámetro
    $stmt->bind_param("i", $productos_id);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener el resultado
    $resultado = $stmt->get_result();

    // Verificar si se obtuvieron resultados
    if ($resultado->num_rows > 0) {
        // Obtener todos los registros
        $filas = $resultado->fetch_all(MYSQLI_ASSOC);
        return $filas;
    } else {
        return null; // No se encontraron resultados
    }

    // Cerrar la sentencia
    $stmt->close();
}

?>
