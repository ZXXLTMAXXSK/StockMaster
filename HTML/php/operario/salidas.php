<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$response = array();

try {
    $sql = "SELECT 
                sm.idSalida,
                sm.fechaSalida,
                sm.numeroPedido,
                sm.estado,
                c.nombre as nombre_cliente,
                COUNT(smd.idDetalle) as total_productos
            FROM salida_mercancia sm
            LEFT JOIN clientes c ON sm.idCliente = c.idCliente
            LEFT JOIN salida_mercancia_detalle smd ON sm.idSalida = smd.idSalida
            GROUP BY sm.idSalida
            ORDER BY sm.fechaSalida DESC";
    
    $result = $conn->query($sql);
    
    $salidas = array();
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $salidas[] = $row;
        }
    }
    
    echo json_encode($salidas);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array('error' => 'Error al obtener salidas: ' . $e->getMessage()));
}

$conn->close();
?>