<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $idSalida = $input['idSalida'];
    
    // En un sistema real, aquí se obtendría el ID del gerente de la sesión
    $idGerente = 3; // Por ejemplo, el gerente con ID 3

    if (empty($idSalida)) {
        $response['message'] = 'ID de salida no proporcionado';
        echo json_encode($response);
        exit;
    }
    
    try {
        // Actualizar el estado de la salida a 'autorizado'
        $sql = "UPDATE salida_mercancia SET estado = 'autorizado', idGerente = ? WHERE idSalida = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $idGerente, $idSalida);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Salida autorizada correctamente';
        } else {
            $response['message'] = 'Error al autorizar la salida';
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Método no permitido';
}

echo json_encode($response);
$conn->close();
?>