<?php
// Cierra las sesiones independientes que puede tener abiertas el navegador.
foreach (['admin_session', 'club_session', 'login_session'] as $nombreSesion) {
	session_name($nombreSesion);
	session_start();
	$_SESSION = [];

	if (ini_get('session.use_cookies')) {
		$parametros = session_get_cookie_params();
		setcookie($nombreSesion, '', time() - 42000, $parametros['path'], $parametros['domain'], $parametros['secure'], $parametros['httponly']);
	}

	session_destroy();
}

// Devuelve al usuario al formulario de inicio de sesión.
header('Location: index.php');
exit;
