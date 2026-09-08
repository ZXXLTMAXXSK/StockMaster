<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fechaSalida = $_POST['fechaSalida'];
    $numeroPedido = $_POST['numeroPedido'];
    $idCliente = $_POST['idCliente'];
    $idProducto = $_POST['idProducto'];
    $cantidad = $_POST['cantidad'];
    
    // Validaciones básicas
    if (empty($fechaSalida) || empty($numeroPedido) || empty($idCliente) || empty($idProducto) || empty($cantidad)) {
        $response['message'] = 'Todos los campos obligatorios deben ser completados';
        echo json_encode($response);
        exit;
    }
    
    try {
        // Insertar en salida_mercancia
        $sql_salida = "INSERT INTO salida_mercancia (fechaSalida, numeroPedido, idCliente, estado) 
                      VALUES (?, ?, ?, 'pendiente')";
        $stmt_salida = $conn->prepare($sql_salida);
        $stmt_salida->bind_param("ssi", $fechaSalida, $numeroPedido, $idCliente);
        
        if ($stmt_salida->execute()) {
            $idSalida = $stmt_salida->insert_id;
            
            // Buscar mercancía disponible para este producto
            $sql_mercancia = "SELECT idMercancia, cantidadUnidades 
                             FROM mercancia 
                             WHERE idProducto = ? AND cantidadUnidades >= ? 
                             LIMIT 1";
            $stmt_mercancia = $conn->prepare($sql_mercancia);
            $stmt_mercancia->bind_param("ii", $idProducto, $cantidad);
            $stmt_mercancia->execute();
            $result_mercancia = $stmt_mercancia->get_result();
            
            if ($result_mercancia->num_rows > 0) {
                $mercancia = $result_mercancia->fetch_assoc();
                $idMercancia = $mercancia['idMercancia'];
                
                // Insertar en salida_mercancia_detalle
                $sql_detalle = "INSERT INTO salida_mercancia_detalle (idSalida, idMercancia, cantidad) 
                               VALUES (?, ?, ?)";
                $stmt_detalle = $conn->prepare($sql_detalle);
                $stmt_detalle->bind_param("iii", $idSalida, $idMercancia, $cantidad);
                
                if ($stmt_detalle->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Salida registrada correctamente';
                } else {
                    $response['message'] = 'Error al registrar el detalle de salida';
                }
            } else {
                $response['message'] = 'No hay suficiente stock disponible para este producto';
            }
        } else {
            $response['message'] = 'Error al registrar la salida';
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