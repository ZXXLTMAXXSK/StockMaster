<?php
header('Content-Type: application/json');
include '../config/conexion.php';

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $rol = $_POST['rol'];
    $contraseña = $_POST['contraseña'];
    
    // Campos opcionales según el rol
    $turno = ($rol == 'operario') ? trim($_POST['turno']) : NULL;
    $departamento = ($rol == 'gerente') ? trim($_POST['departamento']) : NULL;
    
    // Validaciones básicas
    if (empty($nombre) || empty($email) || empty($rol) || empty($contraseña)) {
        $response['message'] = 'Todos los campos obligatorios deben ser completados';
        echo json_encode($response);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'El formato del email no es válido';
        echo json_encode($response);
        exit;
    }
    
    try {
        // Verificar si el email ya existe
        $check_sql = "SELECT idUsuario FROM usuarios WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $response['message'] = 'El email ya está registrado';
            echo json_encode($response);
            exit;
        }
        
        // Insertar nuevo usuario
        $insert_sql = "INSERT INTO usuarios (nombre, email, telefono, rol, turno, departamento, contraseña) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ssssssi", $nombre, $email, $telefono, $rol, $turno, $departamento, $contraseña);
        
        if ($insert_stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Usuario registrado correctamente';
        } else {
            $response['message'] = 'Error al registrar el usuario: ' . $conn->error;
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