<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../acceso_denegado.php");
    exit();
}
?>
