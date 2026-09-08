<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$response = array();

// Debug: Registrar la solicitud recibida
error_log("Solicitud recibida para idCliente: " . ($_GET['id'] ?? 'NO PROPORCIONADO'));

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $idCliente = intval($_GET['id']); // Convertir a entero para seguridad
    
    try {
        $sql = "SELECT idCliente, nombre, email, telefono, direccion, ciudad, fecha_registro 
                FROM clientes 
                WHERE idCliente = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idCliente);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $cliente = $result->fetch_assoc();
            
            // Debug: Registrar qué cliente se encontró
            error_log("Cliente encontrado: ID " . $cliente['idCliente'] . " - " . $cliente['nombre']);
            
            echo json_encode($cliente);
        } else {
            http_response_code(404);
            echo json_encode(array(
                'error' => 'Cliente no encontrado',
                'id_buscado' => $idCliente // Para debugging
            ));
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array('error' => 'Error al obtener perfil: ' . $e->getMessage()));
    }
} else {
    http_response_code(400);
    echo json_encode(array('error' => 'ID de cliente no proporcionado'));
}

$conn->close();
?>