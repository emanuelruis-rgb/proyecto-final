<?php
// Solo el administrador puede ejecutar la eliminación de un partido.
// Usa una sesión independiente para las páginas de administración.
session_name('admin_session');
// Recupera los datos de la sesión actual.
session_start();
if (!isset($_SESSION['nombre'])) {
    // Si no hay un administrador autenticado, vuelve a la página de inicio.
    header('Location: /proyecto-final/index.php');
    exit;
}

// Carga la conexión a la base de datos y las funciones de gestión de partidos.
include(__DIR__ . "/../../conexion-bd/conexion.php");
include(__DIR__ . "/fixtures-data.php");

// Abre la conexión que utilizará la función de eliminación.
$con = connection();

// Comprueba si se recibió el identificador del partido en la URL.
if (isset($_GET['id'])) {
    // Convierte el identificador a entero y elimina el partido correspondiente.
    eliminarPartido($con, (int)$_GET['id']);
}

// Regresa al listado de partidos tras procesar la solicitud.
header("Location: fixtures.php");
exit;
