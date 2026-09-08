<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $idCliente = $input['idCliente'];
    $items = $input['items'];
    
    if (empty($idCliente) || empty($items) || !is_array($items)) {
        $response['message'] = 'Datos del pedido incompletos';
        echo json_encode($response);
        exit;
    }
    
    try {
        // Calcular total del pedido
        $totalMonto = 0;
        foreach ($items as $item) {
            $totalMonto += $item['precio'] * $item['cantidad'];
        }
        
        // Insertar el pedido
        $sql_pedido = "INSERT INTO pedido (idCliente, fechaPedido, estado, totalMonto) 
                      VALUES (?, CURDATE(), 'pendiente', ?)";
        $stmt_pedido = $conn->prepare($sql_pedido);
        $stmt_pedido->bind_param("id", $idCliente, $totalMonto);
        
        if ($stmt_pedido->execute()) {
            $idPedido = $stmt_pedido->insert_id;
            
            // Insertar items del pedido
            foreach ($items as $item) {
                $sql_item = "INSERT INTO item_pedido (idPedido, idProducto, cantidadSolicitada, precio) 
                            VALUES (?, ?, ?, ?)";
                $stmt_item = $conn->prepare($sql_item);
                $stmt_item->bind_param("iiid", $idPedido, $item['idProducto'], $item['cantidad'], $item['precio']);
                $stmt_item->execute();
                $stmt_item->close();
            }
            
            $response['success'] = true;
            $response['message'] = 'Pedido creado correctamente';
            $response['idPedido'] = $idPedido;
        } else {
            $response['message'] = 'Error al crear el pedido';
        }
        
        $stmt_pedido->close();
        
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Método no permitido';
}

echo json_encode($response);
$conn->close();
?>