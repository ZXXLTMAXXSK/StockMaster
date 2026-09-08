<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$response = array();

if (isset($_GET['id'])) {
    $idCliente = $_GET['id'];
    
    try {
        $sql = "SELECT 
                    p.idPedido,
                    p.fechaPedido,
                    p.estado,
                    p.totalMonto
                FROM pedido p
                WHERE p.idCliente = ?
                ORDER BY p.fechaPedido DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idCliente);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $pedidos = array();
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $pedidos[] = $row;
            }
        }
        
        echo json_encode($pedidos);
        $stmt->close();
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array('error' => 'Error al obtener pedidos: ' . $e->getMessage()));
    }
} else {
    http_response_code(400);
    echo json_encode(array('error' => 'ID de cliente no proporcionado'));
}

$conn->close();
?>