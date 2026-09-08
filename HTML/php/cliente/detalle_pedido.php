<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$response = array();

if (isset($_GET['id'])) {
    $idPedido = $_GET['id'];
    
    try {
        // Obtener información básica del pedido
        $sql_pedido = "SELECT 
                    p.idPedido,
                    p.fechaPedido,
                    p.estado,
                    p.totalMonto,
                    c.nombre as nombre_cliente
                FROM pedido p
                INNER JOIN clientes c ON p.idCliente = c.idCliente
                WHERE p.idPedido = ?";
        
        $stmt_pedido = $conn->prepare($sql_pedido);
        $stmt_pedido->bind_param("i", $idPedido);
        $stmt_pedido->execute();
        $result_pedido = $stmt_pedido->get_result();
        
        $pedido = $result_pedido->fetch_assoc();
        
        // Obtener items del pedido
        $sql_items = "SELECT 
                    ip.*,
                    p.nombre as nombre_producto,
                    p.sku
                FROM item_pedido ip
                INNER JOIN productos p ON ip.idProducto = p.idProducto
                WHERE ip.idPedido = ?";
        
        $stmt_items = $conn->prepare($sql_items);
        $stmt_items->bind_param("i", $idPedido);
        $stmt_items->execute();
        $result_items = $stmt_items->get_result();
        
        $items = array();
        while($row = $result_items->fetch_assoc()) {
            $items[] = $row;
        }
        
        $response = array(
            'pedido' => $pedido,
            'items' => $items
        );
        
        echo json_encode($response);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array('error' => 'Error al obtener detalle de pedido: ' . $e->getMessage()));
    }
} else {
    http_response_code(400);
    echo json_encode(array('error' => 'ID de pedido no proporcionado'));
}

$conn->close();
?>