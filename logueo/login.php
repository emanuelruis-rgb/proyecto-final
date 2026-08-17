<?php
include "../conexion-bd/conexion.php";

session_start();

$nombre = $_POST['nombre'];
$contraseña = $_POST['contraseña'];

$_SESSION['nombre'] = $nombre;

$consultaadmin = "SELECT * FROM administrador WHERE nombreUsuario='$nombre' AND contraseña='$contraseña'";
$resultadoadmin = mysqli_query($conexion, $consultaadmin);

$consultaclub = "SELECT * FROM club WHERE nombreClub='$nombre' AND contraseñaClub='$contraseña'";
$resultadoclub = mysqli_query($conexion, $consultaclub);

if (mysqli_num_rows($resultadoadmin) > 0) {
    header("Location: ../admin/indexadmin.php");
    exit();
} else {
    if (mysqli_num_rows($resultadoclub) > 0) {
        header("Location: ../usuario/indexusuario.php");
        exit();
    } else {
        $_SESSION["error"] = "Usuario o contraseña incorrectos.";
        header("Location: ../index.php");
        exit();
    }
}

?>