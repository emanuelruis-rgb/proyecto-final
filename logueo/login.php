<?php
session_start();
include '../conexion-bd/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre   = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conexion->prepare("SELECT * FROM administrador WHERE nombre = ? AND contraseña = ?");
    $stmt->bind_param("ss", $nombre, $password);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $_SESSION['usuario'] = $nombre;
        header('Location: ../admin/indexadmin.php');
        exit();
    } else {
        $error = true;
    }
}
?>