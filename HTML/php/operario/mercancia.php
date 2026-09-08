<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$response = array();

try {
    $sql = "SELECT 
                m.idMercancia,
                p.nombre as nombre_producto,
                p.sku,
                m.cantidadUnidades,
                m.peso,
                m.ubicacion,
                m.fechaIngreso
            FROM mercancia m
            INNER JOIN productos p ON m.idProducto = p.idProducto
            ORDER BY m.fechaIngreso DESC";
    
    $result = $conn->query($sql);
    
    $mercancia = array();
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $mercancia[] = $row;
        }
    }
    
    echo json_encode($mercancia);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array('error' => 'Error al obtener mercancía: ' . $e->getMessage()));
}

$conn->close();
?>