<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $sku = trim($_POST['sku']);
    $precio = floatval($_POST['precio']);
    $idProveedor = !empty($_POST['idProveedor']) ? intval($_POST['idProveedor']) : NULL;
    
    // Validaciones básicas
    if (empty($nombre) || empty($sku) || $precio <= 0) {
        $response['message'] = 'Nombre, SKU y precio son obligatorios. El precio debe ser mayor a 0.';
        echo json_encode($response);
        exit;
    }
    
    if ($precio <= 0) {
        $response['message'] = 'El precio debe ser mayor a 0';
        echo json_encode($response);
        exit;
    }
    
    try {
        // Verificar si el SKU ya existe
        $check_sql = "SELECT idProducto FROM productos WHERE sku = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $sku);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $response['message'] = 'Ya existe un producto con ese SKU';
            echo json_encode($response);
            exit;
        }
        
        // Verificar que el proveedor existe si se proporcionó
        if ($idProveedor) {
            $check_proveedor_sql = "SELECT idProveedor FROM proveedores WHERE idProveedor = ?";
            $check_proveedor_stmt = $conn->prepare($check_proveedor_sql);
            $check_proveedor_stmt->bind_param("i", $idProveedor);
            $check_proveedor_stmt->execute();
            $check_proveedor_result = $check_proveedor_stmt->get_result();
            
            if ($check_proveedor_result->num_rows == 0) {
                $response['message'] = 'El proveedor seleccionado no existe';
                echo json_encode($response);
                exit;
            }
        }
        
        // Insertar nuevo producto
        $insert_sql = "INSERT INTO productos (nombre, descripcion, sku, precio, idProveedor) 
                      VALUES (?, ?, ?, ?, ?)";
        
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("sssdi", $nombre, $descripcion, $sku, $precio, $idProveedor);
        
        if ($insert_stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Producto registrado correctamente';
        } else {
            $response['message'] = 'Error al registrar el producto: ' . $conn->error;
        }
        
        $insert_stmt->close();
        
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Método no permitido';
}

echo json_encode($response);
$conn->close();
?>