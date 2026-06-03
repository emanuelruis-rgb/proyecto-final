<?php
session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['usuario'];
    $password = $_POST['password'];

    $query = "SELECT * FROM usuarios WHERE nombre='$nombre' AND contraseña='$password'";
    $resultado = $conexion->query($query);

    if ($resultado->num_rows > 0) {
        $_SESSION['usuario'] = $nombre;
        header('Location: indexadmin.php'); // cambiá por tu página principal
        exit();
    } else {
        echo "<script>alert('Usuario o contraseña incorrectos');</script>";
    }
}
?>