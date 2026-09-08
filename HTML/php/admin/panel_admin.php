<?php
header('Content-Type: application/json');
include 'config/conexion.php';

$response = array(
    'totales' => array(),
    'actividades' => array()
);

try {
    // Total de productos
    $sql_productos = "SELECT COUNT(*) as total FROM productos";
    $result = $conn->query($sql_productos);
    $totales['productos'] = $result->fetch_assoc()['total'];
    
    // Total de pedidos
    $sql_pedidos = "SELECT COUNT(*) as total FROM pedido";
    $result = $conn->query($sql_pedidos);
    $totales['pedidos'] = $result->fetch_assoc()['total'];
    
    // Entradas pendientes
    $sql_entradas = "SELECT COUNT(*) as total FROM entrada_mercancia WHERE estado = 'pendiente'";
    $result = $conn->query($sql_entradas);
    $totales['entradas'] = $result->fetch_assoc()['total'];
    
    // Alertas de inventario (productos con stock bajo - ejemplo: menos de 10 unidades)
    $sql_alertas = "SELECT COUNT(*) as total FROM inventario_producto WHERE cantidadActiva < 10";
    $result = $conn->query($sql_alertas);
    $totales['alertas'] = $result->fetch_assoc()['total'];
    
    $response['totales'] = $totales;
    
    // Actividades recientes (ejemplo combinado)
    $actividades = array();
    
    // Últimos usuarios registrados
    $sql_actividades = "SELECT fecha_creacion as fecha, 'Usuario registrado' as evento, nombre as usuario 
                       FROM usuarios 
                       ORDER BY fecha_creacion DESC 
                       LIMIT 3";
    $result = $conn->query($sql_actividades);
    while($row = $result->fetch_assoc()) {
        $actividades[] = $row;
    }
    
    $response['actividades'] = $actividades;
    
} catch (Exception $e) {
    http_response_code(500);
    $response['error'] = 'Error al cargar datos: ' . $e->getMessage();
}

echo json_encode($response);
$conn->close();
?>