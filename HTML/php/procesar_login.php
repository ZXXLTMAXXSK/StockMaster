<?php
session_start();
require_once "config/conexion.php";

if (empty($_POST['email']) || empty($_POST['password']) || empty($_POST['role'])) {
    header("Location: ../login.html?error=campos_vacios");
    exit();
}

$email = trim($_POST['email']);
$password = trim($_POST['password']);
$role = trim($_POST['role']);

// CLIENTE
if ($role === 'cliente') {
    $sql = "SELECT * FROM clientes WHERE email = ? AND contraseña = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $cliente = $resultado->fetch_assoc();
        $_SESSION['usuario'] = $cliente['nombre'];
        $_SESSION['rol'] = 'cliente';
        header("Location: ../panel_cliente.html");
        exit();
    } else {
        header("Location: ../login.html?error=credenciales_invalidas");
        exit();
    }
}

else {
    $sql = "SELECT * FROM usuarios WHERE email = ? AND contraseña = ? AND rol = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $email, $password, $role);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        if (!$usuario['activo']) {
            header("Location: ../login.html?error=usuario_inactivo");
            exit();
        }

        $_SESSION['usuario'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];

        switch ($usuario['rol']) {
            case 'administrador':
                header("Location: ../panel_admin.html");
                break;
            case 'operario':
                header("Location: ../panel_operario.html");
                break;
            case 'gerente':
                header("Location: ../panel_gerente.html");
                break;
        }
        exit();
    } else {
        header("Location: ../login.html?error=credenciales_invalidas");
        exit();
    }
}
?>
