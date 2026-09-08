<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$response = array();

try {
    // Obtener el inventario más reciente
    $sql_inventario = "SELECT idInventario FROM inventario ORDER BY fechaActualizacion DESC LIMIT 1";
    $result_inventario = $conn->query($sql_inventario);
    
    if ($result_inventario->num_rows > 0) {
        $inventario_actual = $result_inventario->fetch_assoc();
        $idInventario = $inventario_actual['idInventario'];
        
        // Obtener los productos del inventario actual
        $sql = "SELECT 
                    p.idProducto,
                    p.nombre,
                    p.sku,
                    p.precio,
                    ip.cantidadActiva,
                    pr.nombre as nombre_proveedor
                FROM inventario_producto ip
                INNER JOIN productos p ON ip.idProducto = p.idProducto
                LEFT JOIN proveedores pr ON p.idProveedor = pr.idProveedor
                WHERE ip.idInventario = ?
                ORDER BY p.nombre ASC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idInventario);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $inventario = array();
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $inventario[] = $row;
            }
        }
        
        echo json_encode($inventario);
        $stmt->close();
    } else {
        echo json_encode(array());
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array('error' => 'Error al obtener reporte de inventario: ' . $e->getMessage()));
}

$conn->close();
?>