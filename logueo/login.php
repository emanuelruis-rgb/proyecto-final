<?php
include "../conexion-bd/conexion.php";

session_start();

$nombre = $_POST['nombre'] ?? '';
$contraseña = $_POST['contraseña'] ?? '';

$_SESSION['nombre'] = $nombre;

$consultaUsuario = "SELECT * FROM usuario WHERE nombre = '$nombre' AND contraseña = '$contraseña' LIMIT 1";
$resultadoUsuario = mysqli_query($conexion, $consultaUsuario);

if (mysqli_num_rows($resultadoUsuario) > 0) {
    $usuario = mysqli_fetch_assoc($resultadoUsuario);

    if ($usuario['rol'] == 'administrador') {
        header("Location: ../admin/indexadmin.php");
        exit();
    }

    if ($usuario['rol'] == 'club') {
        header("Location: ../usuario/indexusuario.php");
        exit();
    }
}

$_SESSION["error"] = "Usuario o contraseña incorrectos.";
header("Location: ../index.php");
exit();

?>