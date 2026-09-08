<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$response = array();

if (isset($_GET['id'])) {
    $idEntrada = $_GET['id'];
    
    try {
        // Obtener información básica de la entrada
        $sql_entrada = "SELECT 
                    em.*,
                    p.nombre as nombre_proveedor,
                    u.nombre as nombre_gerente
                FROM entrada_mercancia em
                LEFT JOIN proveedores p ON em.idProveedor = p.idProveedor
                LEFT JOIN usuarios u ON em.idGerente = u.idUsuario
                WHERE em.idEntrada = ?";
        
        $stmt_entrada = $conn->prepare($sql_entrada);
        $stmt_entrada->bind_param("i", $idEntrada);
        $stmt_entrada->execute();
        $result_entrada = $stmt_entrada->get_result();
        
        $entrada = $result_entrada->fetch_assoc();
        
        // Obtener detalles de los productos
        $sql_detalles = "SELECT 
                    emd.*,
                    p.nombre as nombre_producto,
                    p.sku,
                    m.peso,
                    m.ubicacion
                FROM entrada_mercancia_detalle emd
                INNER JOIN mercancia m ON emd.idMercancia = m.idMercancia
                INNER JOIN productos p ON m.idProducto = p.idProducto
                WHERE emd.idEntrada = ?";
        
        $stmt_detalles = $conn->prepare($sql_detalles);
        $stmt_detalles->bind_param("i", $idEntrada);
        $stmt_detalles->execute();
        $result_detalles = $stmt_detalles->get_result();
        
        $detalles = array();
        while($row = $result_detalles->fetch_assoc()) {
            $detalles[] = $row;
        }
        
        $response = array(
            'entrada' => $entrada,
            'detalles' => $detalles
        );
        
        echo json_encode($response);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array('error' => 'Error al obtener detalle de entrada: ' . $e->getMessage()));
    }
} else {
    http_response_code(400);
    echo json_encode(array('error' => 'ID de entrada no proporcionado'));
}

$conn->close();
?>