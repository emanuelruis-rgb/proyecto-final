<?php

// Cada rol usa una sesión distinta; se invalidan todas para cerrar también
// una sesión anterior de otro rol abierta en el mismo navegador.
foreach (['admin_session', 'club_session', 'login_session'] as $nombreSesion) {
	session_name($nombreSesion);
	session_start();
	$_SESSION = [];

	if (ini_get('session.use_cookies')) {
		$parametros = session_get_cookie_params();
		// Además de destruir la sesión en el servidor, expira la cookie del navegador.
		setcookie($nombreSesion, '', time() - 42000, $parametros['path'], $parametros['domain'], $parametros['secure'], $parametros['httponly']);
	}

	session_destroy();
}

// Evita que una recarga del formulario repita la solicitud de cierre.
header('Location: index.php');
exit;
