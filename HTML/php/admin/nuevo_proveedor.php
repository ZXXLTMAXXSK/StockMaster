<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    
    // Validaciones básicas
    if (empty($nombre)) {
        $response['message'] = 'El nombre del proveedor es obligatorio';
        echo json_encode($response);
        exit;
    }
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'El formato del email no es válido';
        echo json_encode($response);
        exit;
    }
    
    try {
        // Verificar si el proveedor ya existe (por nombre)
        $check_sql = "SELECT idProveedor FROM proveedores WHERE nombre = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $nombre);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $response['message'] = 'Ya existe un proveedor con ese nombre';
            echo json_encode($response);
            exit;
        }
        
        // Insertar nuevo proveedor
        $insert_sql = "INSERT INTO proveedores (nombre, email, telefono, direccion) 
                      VALUES (?, ?, ?, ?)";
        
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ssss", $nombre, $email, $telefono, $direccion);
        
        if ($insert_stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Proveedor registrado correctamente';
        } else {
            $response['message'] = 'Error al registrar el proveedor: ' . $conn->error;
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