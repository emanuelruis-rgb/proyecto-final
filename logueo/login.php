<?php
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
} else {  //acá es si detecta q se ingreso como club.
    if (mysqli_num_rows($resultadoClub) > 0) {
        session_name('club_session');
        session_start();
        // el nombre del club queda guaradado como "nombre", ed ID como "idClub". esto se puede 
        //usar en cualquier lado de la aplicacion web.
        $club = mysqli_fetch_assoc($resultadoClub);
        
        $_SESSION['nombre'] = $club['nombreClub'];
        $_SESSION['idClub'] = $club['idClub'];
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