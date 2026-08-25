<?php
// Carga la conexión compartida con la base de datos.
include "../conexion-bd/conexion.php";
$conexion = connection();

// Inicia la sesión para conservar el usuario y los mensajes de error.
session_start();

// Obtiene las credenciales enviadas por el formulario de acceso.
$nombre = $_POST['nombre'];
$contraseña = $_POST['contraseña'];

// Guarda temporalmente el nombre introducido por el usuario.
$_SESSION['nombre'] = $nombre;

// Busca una cuenta que coincida con las credenciales recibidas.
$consultaClub = "SELECT * FROM club WHERE nombreClub = '$nombre' AND contraseñaClub = '$contraseña'";
$resultadoClub = mysqli_query($conexion, $consultaClub);

$consultaAdmin = "SELECT * FROM administrador WHERE nombreUsuario = '$nombre' AND contraseña = '$contraseña'";
$resultadoAdmin = mysqli_query($conexion, $consultaAdmin);

// Redirige a la vista correspondiente según el rol de la cuenta.
if (mysqli_num_rows($resultadoAdmin) > 0) {
    header("Location: ../admin/indexadmin.php");
    exit();
} else {
    if (mysqli_num_rows($resultadoClub) > 0) {
        header("Location: ../usuario/indexusuario.php");
        exit();
    } else {
        // Informa del acceso fallido y devuelve al formulario de inicio.
        $_SESSION["error"] = "Usuario o contraseña incorrectos.";
        header("Location: ../index.php");
        exit();
    }
}
?>