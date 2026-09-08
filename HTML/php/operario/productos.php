<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$response = array();

try {
    $sql = "SELECT idProducto, nombre, sku FROM productos ORDER BY nombre ASC";
    $result = $conn->query($sql);
    
    $productos = array();
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
    }
    
    echo json_encode($productos);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array('error' => 'Error al obtener productos: ' . $e->getMessage()));
}

$conn->close();
?>