<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$response = array();

try {
    $sql = "SELECT idUsuario, nombre, email, telefono, rol, activo, turno, departamento, fecha_creacion 
            FROM usuarios 
            ORDER BY fecha_creacion DESC";
    
    $result = $conn->query($sql);
    
    $usuarios = array();
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
    }
    
    echo json_encode($usuarios);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array('error' => 'Error al obtener usuarios: ' . $e->getMessage()));
}

$conn->close();
?>