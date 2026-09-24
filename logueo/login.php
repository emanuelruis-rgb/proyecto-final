<?php
// Carga la conexión compartida con la base de datos.
include "../conexion-bd/conexion.php";
$conexion = connection();

// Obtiene las credenciales enviadas por el formulario de acceso.
$nombre = $_POST['nombre'];
$contraseña = $_POST['contraseña'];

// Busca una cuenta que coincida con las credenciales recibidas.
$consultaClub = "SELECT * FROM club WHERE nombreClub = '$nombre' AND contraseñaClub = '$contraseña'";
$resultadoClub = mysqli_query($conexion, $consultaClub);

$consultaAdmin = "SELECT * FROM administrador WHERE nombreUsuario = '$nombre' AND contraseña = '$contraseña'";
$resultadoAdmin = mysqli_query($conexion, $consultaAdmin);

// Redirige a la vista correspondiente según el rol de la cuenta.
if (mysqli_num_rows($resultadoAdmin) > 0) {
    session_name('admin_session');
    session_start();
    $_SESSION['nombre'] = $nombre;
    // Guarda el administrador para atribuirle los boletines que publique.
    $admin = mysqli_fetch_assoc($resultadoAdmin);
    $_SESSION['idAdmin'] = $admin['idAdmin'];
    header("Location: ../admin/indexadmin.php");
    exit();
} else {
    if (mysqli_num_rows($resultadoClub) > 0) {
        session_name('club_session');
        session_start();
        $_SESSION['nombre'] = $nombre;
        header("Location: ../club/indexclub.php");
        exit();
    } else {
        // Informa del acceso fallido y devuelve al formulario de inicio.
        session_name('login_session');
        session_start();
        $_SESSION["error"] = "Usuario o contraseña incorrectos.";
        header("Location: ../index.php");
        exit();
    }
}
?>