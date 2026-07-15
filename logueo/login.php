<?php
include "../conexion-bd/conexion.php";

session_start();

$nombre = $_POST['nombre'];
$contraseña = $_POST['contraseña'];

$_SESSION['nombre'] = $nombre;

$consulta = "SELECT * FROM usuario WHERE nombre='$nombre' AND contraseña='$contraseña'";
$resultado = mysqli_query($conexion, $consulta);

$filas = mysqli_fetch_array($resultado);

if ($filas) {

    if ($filas['rol'] == "administrador") {
        header("Location: ../admin/indexadmin.php");
        exit();
    } elseif ($filas['rol'] == "club") {
        header("Location: ../usuario/indexusuario.php");
        exit();
    }

} else {
    $_SESSION['error'] = "Usuario o contraseña incorrectos.";
    header("Location: ../index.php");
    exit();
}

mysqli_free_result($resultado);
mysqli_close($conexion);
?>