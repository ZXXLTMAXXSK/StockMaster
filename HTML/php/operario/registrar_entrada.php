<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fechaEntrada = $_POST['fechaEntrada'];
    $numeroFactura = $_POST['numeroFactura'];
    $idProveedor = $_POST['idProveedor'];
    $idProducto = $_POST['idProducto'];
    $cantidad = $_POST['cantidad'];
    $peso = $_POST['peso'];
    $ubicacion = $_POST['ubicacion'];
    
    // Validaciones básicas
    if (empty($fechaEntrada) || empty($numeroFactura) || empty($idProveedor) || empty($idProducto) || empty($cantidad)) {
        $response['message'] = 'Todos los campos obligatorios deben ser completados';
        echo json_encode($response);
        exit;
    }
    
    try {
        // Insertar en entrada_mercancia
        $sql_entrada = "INSERT INTO entrada_mercancia (fechaEntrada, numeroFactura, idProveedor, estado) 
                       VALUES (?, ?, ?, 'pendiente')";
        $stmt_entrada = $conn->prepare($sql_entrada);
        $stmt_entrada->bind_param("ssi", $fechaEntrada, $numeroFactura, $idProveedor);
        
        if ($stmt_entrada->execute()) {
            $idEntrada = $stmt_entrada->insert_id;
            
            // Insertar en mercancia
            $sql_mercancia = "INSERT INTO mercancia (idProducto, cantidadUnidades, peso, ubicacion, fechaIngreso) 
                             VALUES (?, ?, ?, ?, ?)";
            $stmt_mercancia = $conn->prepare($sql_mercancia);
            $stmt_mercancia->bind_param("iidss", $idProducto, $cantidad, $peso, $ubicacion, $fechaEntrada);
            
            if ($stmt_mercancia->execute()) {
                $idMercancia = $stmt_mercancia->insert_id;
                
                // Insertar en entrada_mercancia_detalle
                $sql_detalle = "INSERT INTO entrada_mercancia_detalle (idEntrada, idMercancia, cantidad) 
                               VALUES (?, ?, ?)";
                $stmt_detalle = $conn->prepare($sql_detalle);
                $stmt_detalle->bind_param("iii", $idEntrada, $idMercancia, $cantidad);
                
                if ($stmt_detalle->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Entrada registrada correctamente';
                } else {
                    $response['message'] = 'Error al registrar el detalle de entrada';
                }
            } else {
                $response['message'] = 'Error al registrar la mercancía';
            }
        } else {
            $response['message'] = 'Error al registrar la entrada';
        }
        
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Método no permitido';
}

echo json_encode($response);
$conn->close();
?>