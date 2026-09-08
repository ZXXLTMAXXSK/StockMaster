<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $idPedido = $input['idPedido'];
    
    if (empty($idPedido)) {
        $response['message'] = 'ID de pedido no proporcionado';
        echo json_encode($response);
        exit;
    }
    
    try {
        // Verificar que el pedido esté en estado pendiente
        $sql_verificar = "SELECT estado FROM pedido WHERE idPedido = ?";
        $stmt_verificar = $conn->prepare($sql_verificar);
        $stmt_verificar->bind_param("i", $idPedido);
        $stmt_verificar->execute();
        $result_verificar = $stmt_verificar->get_result();
        
        if ($result_verificar->num_rows > 0) {
            $pedido = $result_verificar->fetch_assoc();
            
            if ($pedido['estado'] === 'pendiente') {
                // Cancelar el pedido
                $sql_cancelar = "UPDATE pedido SET estado = 'cancelado' WHERE idPedido = ?";
                $stmt_cancelar = $conn->prepare($sql_cancelar);
                $stmt_cancelar->bind_param("i", $idPedido);
                
                if ($stmt_cancelar->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Pedido cancelado correctamente';
                } else {
                    $response['message'] = 'Error al cancelar el pedido';
                }
                
                $stmt_cancelar->close();
            } else {
                $response['message'] = 'No se puede cancelar un pedido que no está pendiente';
            }
        } else {
            $response['message'] = 'Pedido no encontrado';
        }
        
        $stmt_verificar->close();
        
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Método no permitido';
}

echo json_encode($response);
$conn->close();
?>