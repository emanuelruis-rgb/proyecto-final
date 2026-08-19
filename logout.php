<?php
// Inicia la sesión actual para poder cerrarla correctamente.
session_start();

// Elimina los datos almacenados y finaliza la sesión.
session_unset();
session_destroy();

// Devuelve al usuario al formulario de inicio de sesión.
header('Location: index.php');
exit;
