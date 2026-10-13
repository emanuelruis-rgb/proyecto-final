<?php
// Solo el administrador puede ejecutar la eliminación de un partido.
session_name('admin_session');
session_start();
if (!isset($_SESSION['nombre'])) {
    header('Location: /proyecto-final/index.php');
    exit;
}

include(__DIR__ . "/../../conexion-bd/conexion.php");
include(__DIR__ . "/fixtures-data.php");

$con = connection();

if (isset($_GET['id'])) {
    eliminarPartido($con, (int)$_GET['id']);
}

header("Location: fixtures.php");
exit;
