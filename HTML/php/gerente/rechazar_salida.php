<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $idSalida = $input['idSalida'];
    $motivo = $input['motivo'];
    
    $idGerente = 3; // ID del gerente desde sesión

    if (empty($idSalida) || empty($motivo)) {
        $response['message'] = 'Datos incompletos';
        echo json_encode($response);
        exit;
    }
    
    try {
        // Actualizar el estado de la salida a 'rechazado'
        $sql = "UPDATE salida_mercancia SET estado = 'rechazado', idGerente = ? WHERE idSalida = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $idGerente, $idSalida);
        
        if ($stmt->execute()) {
            // Aquí podrías guardar el motivo en una tabla de log
            $response['success'] = true;
            $response['message'] = 'Salida rechazada correctamente';
        } else {
            $response['message'] = 'Error al rechazar la salida';
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