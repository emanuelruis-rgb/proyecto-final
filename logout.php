<?php

$sesionesPorRol = [
	'admin' => 'admin_session',
	'club' => 'club_session',
];
$rol = $_GET['rol'] ?? '';

if (!is_string($rol) || !isset($sesionesPorRol[$rol])) {
	http_response_code(400);
	exit('El tipo de sesión no es válido.');
}

$nombreSesion = $sesionesPorRol[$rol];
session_name($nombreSesion);
session_start();
$_SESSION = [];

if (ini_get('session.use_cookies')) {
	$parametros = session_get_cookie_params();
	setcookie($nombreSesion, '', time() - 42000, $parametros['path'], $parametros['domain'], $parametros['secure'], $parametros['httponly']);
}

session_destroy();

header('Location: index.php');
exit;
