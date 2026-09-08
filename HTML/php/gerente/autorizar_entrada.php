<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $idEntrada = $input['idEntrada'];
    
    // En un sistema real, aquí se obtendría el ID del gerente de la sesión
    $idGerente = 3; // Por ejemplo, el gerente con ID 3

    if (empty($idEntrada)) {
        $response['message'] = 'ID de entrada no proporcionado';
        echo json_encode($response);
        exit;
    }
    
    try {
        // Actualizar el estado de la entrada a 'autorizado'
        $sql = "UPDATE entrada_mercancia SET estado = 'autorizado', idGerente = ? WHERE idEntrada = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $idGerente, $idEntrada);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Entrada autorizada correctamente';
        } else {
            $response['message'] = 'Error al autorizar la entrada';
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