<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$response = array();

try {
    $sql = "SELECT 
                em.idEntrada,
                em.fechaEntrada,
                em.numeroFactura,
                em.estado,
                p.nombre as nombre_proveedor,
                COUNT(emd.idDetalle) as total_productos
            FROM entrada_mercancia em
            LEFT JOIN proveedores p ON em.idProveedor = p.idProveedor
            LEFT JOIN entrada_mercancia_detalle emd ON em.idEntrada = emd.idEntrada
            GROUP BY em.idEntrada
            ORDER BY em.fechaEntrada DESC";
    
    $result = $conn->query($sql);
    
    $entradas = array();
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $entradas[] = $row;
        }
    }
    
    echo json_encode($entradas);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array('error' => 'Error al obtener entradas: ' . $e->getMessage()));
}

$conn->close();
?>