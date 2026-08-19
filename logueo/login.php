<?php
// Carga la conexión compartida con la base de datos.
include "../conexion-bd/conexion.php";

// Inicia la sesión para conservar el usuario y los mensajes de error.
session_start();

// Obtiene las credenciales enviadas por el formulario de acceso.
$nombre = $_POST['nombre'] ?? '';
$contraseña = $_POST['contraseña'] ?? '';

// Guarda temporalmente el nombre introducido por el usuario.
$_SESSION['nombre'] = $nombre;

// Busca una cuenta que coincida con las credenciales recibidas.
$consultaUsuario = "SELECT * FROM usuario WHERE nombre = '$nombre' AND contraseña = '$contraseña' LIMIT 1";
$resultadoUsuario = mysqli_query($conexion, $consultaUsuario);

// Redirige a la vista correspondiente según el rol de la cuenta.
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

// Informa del acceso fallido y devuelve al formulario de inicio.
$_SESSION["error"] = "Usuario o contraseña incorrectos.";
header("Location: ../index.php");
exit();

?>