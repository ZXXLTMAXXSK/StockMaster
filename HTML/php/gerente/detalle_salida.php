<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$response = array();

if (isset($_GET['id'])) {
    $idSalida = $_GET['id'];
    
    try {
        // Obtener información básica de la salida
        $sql_salida = "SELECT 
                    sm.*,
                    c.nombre as nombre_cliente,
                    u.nombre as nombre_gerente
                FROM salida_mercancia sm
                LEFT JOIN clientes c ON sm.idCliente = c.idCliente
                LEFT JOIN usuarios u ON sm.idGerente = u.idUsuario
                WHERE sm.idSalida = ?";
        
        $stmt_salida = $conn->prepare($sql_salida);
        $stmt_salida->bind_param("i", $idSalida);
        $stmt_salida->execute();
        $result_salida = $stmt_salida->get_result();
        
        $salida = $result_salida->fetch_assoc();
        
        // Obtener detalles de los productos
        $sql_detalles = "SELECT 
                    smd.*,
                    p.nombre as nombre_producto,
                    p.sku
                FROM salida_mercancia_detalle smd
                INNER JOIN mercancia m ON smd.idMercancia = m.idMercancia
                INNER JOIN productos p ON m.idProducto = p.idProducto
                WHERE smd.idSalida = ?";
        
        $stmt_detalles = $conn->prepare($sql_detalles);
        $stmt_detalles->bind_param("i", $idSalida);
        $stmt_detalles->execute();
        $result_detalles = $stmt_detalles->get_result();
        
        $detalles = array();
        while($row = $result_detalles->fetch_assoc()) {
            $detalles[] = $row;
        }
        
        $response = array(
            'salida' => $salida,
            'detalles' => $detalles
        );
        
        echo json_encode($response);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array('error' => 'Error al obtener detalle de salida: ' . $e->getMessage()));
    }
} else {
    http_response_code(400);
    echo json_encode(array('error' => 'ID de salida no proporcionado'));
}

$conn->close();
?>