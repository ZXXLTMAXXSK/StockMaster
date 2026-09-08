<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$response = array();

try {
    $sql = "SELECT idProveedor, nombre, email, telefono, direccion 
            FROM proveedores 
            ORDER BY nombre ASC";
    
    $result = $conn->query($sql);
    
    $proveedores = array();
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $proveedores[] = $row;
        }
    }
    
    echo json_encode($proveedores);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array('error' => 'Error al obtener proveedores: ' . $e->getMessage()));
}

$conn->close();
?>