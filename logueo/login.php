<?php
require_once __DIR__ . '/../conexion-bd/conexion.php';

session_name('login_session');
session_start();

// Esta función define una redirección única de vuelta a index.php para los mensajes(de éxito o error)
//  y para evitar reenviar formularios.
function volverAlLogin(string $tipo, string $mensaje): void
{
    // Guarda el mensaje en variable de sesión, para mostrarlo una sola vez en la pantalla de login.
    $_SESSION[$tipo] = $mensaje;
    header('Location: ../index.php');
    exit;
}

// Acepta únicamente solicitudes POST con un token CSRF(Token aleatorio único para validar el origen del formulario y prevenir ataques) válido
$tokenPublicado = $_POST['csrf_token'] ?? '';
if (
    //esto verifica el token csrf
    $_SERVER['REQUEST_METHOD'] !== 'POST'
    || !is_string($tokenPublicado)
    || !isset($_SESSION['csrf_token'])
    || !hash_equals($_SESSION['csrf_token'], $tokenPublicado)
) {
    volverAlLogin('error', 'La solicitud venció. Intenta nuevamente.');
}


// Abre la conexión solo después de comprobar el método y el token.
$conexion = connection();
// Lee la acción solicitada y usa el login normal como comportamiento predeterminado.
$accion = $_POST['accion'] ?? 'ingresar';

// Rechaza acciones desconocidas (que no sean ingresar)
if ($accion !== 'ingresar') {
    volverAlLogin('error', 'La acción solicitada no es válida.');
}

//recupera los datos, $nombre (campo de nombre de usuario) y $contraseña (campo de contraseña)
$nombre = $_POST['nombre'] ?? '';
$contrasena = $_POST['contraseña'] ?? '';

// si nombre y contraseña no son strings, o son vacíos, vuelve a login
if (
    !is_string($nombre)
    || !is_string($contrasena)
    || $nombre === ''
    || $contrasena === ''
) {
    volverAlLogin('error', 'Usuario o contraseña incorrectos.');
}


// prepara consulta para  buscar un admin con nombreUsuario del admin y su contraseña
$consultaAdmin = $conexion->prepare(
    'SELECT idAdmin, contraseña FROM administrador WHERE nombreUsuario = ? LIMIT 1'
);

// especifica que deben ser string las variables usadas en la consulta
$consultaAdmin->bind_param('s', $nombre);
$consultaAdmin->execute();
// guarda el resultado en la variable admin
$admin = $consultaAdmin->get_result()->fetch_assoc();
$consultaAdmin->close();


// si los datos de inicio de sesión son de un admin, inicia sesión como admin
if ($admin && password_verify($contrasena, $admin['contraseña'])) {
    session_write_close();
    session_name('admin_session');
    session_start();

    // Renueva el identificador para reducir el riesgo de fijación de sesión.
    session_regenerate_id(true);
    // Guarda el nombre del admin autenticado.
    $_SESSION['nombre'] = $nombre;
    // Guarda el id del admin.
    $_SESSION['idAdmin'] = $admin['idAdmin'];

    header('Location: ../admin/indexadmin.php');
    exit();
}

// Busca un id de club y el nombre del club, usando el nombre y contraseña dado.
$consultaClub = $conexion->prepare(
    'SELECT idClub, nombreClub, contraseñaClub FROM club WHERE nombreClub = ? LIMIT 1');
// especifica que deben ser string las variables usadas en la consulta
$consultaClub->bind_param('s', $nombre);
$consultaClub->execute();

// agarra el resultado de consultaClub y lo pone en $club
$club = $consultaClub->get_result()->fetch_assoc();
$consultaClub->close();

// Inicia sesión como club si se encuentra un club, y si la contraseña ingresada es igual
// al hash ya existente en la bd
if ($club && password_verify($contrasena, $club['contraseñaClub'])) {
    session_write_close();
    session_name('club_session');
    session_start();
    // Renueva el identificador para reducir el riesgo de fijación de sesión.
    session_regenerate_id(true);

    // Guarda el nombre del club para el resto de la aplicación.
    $_SESSION['nombre'] = $club['nombreClub'];
    // Guarda el id para el resto de la aplicación.
    $_SESSION['idClub'] = $club['idClub'];
    header('Location: ../club/indexclub.php');
    exit();
}

//si no se encontro ni admin ni club, vuelve al login con error
volverAlLogin('error', 'Usuario o contraseña incorrectos.');