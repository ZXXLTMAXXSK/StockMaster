<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$response = array();

try {
    $sql = "SELECT idCliente, nombre FROM clientes ORDER BY nombre ASC";
    $result = $conn->query($sql);
    
    $clientes = array();
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $clientes[] = $row;
        }
    }
    
    echo json_encode($clientes);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array('error' => 'Error al obtener clientes: ' . $e->getMessage()));
}

$conn->close();
?>