<?php
include(__DIR__ . "/../conexion-bd/conexion.php");
include(__DIR__ . "/fixtures-data.php");

$con = connection();

if (isset($_GET['id'])) {
    eliminarPartido($con, (int)$_GET['id']);
}

header("Location: fixtures.php");
exit;
