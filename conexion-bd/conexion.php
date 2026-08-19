<?php
// Define los datos de acceso a la base de datos.
$servidor = "localhost";
$usuario = "root";
$clave = "";
$basededatos = "liga-de-futbol";

// Abre la conexión con el servidor MySQL.
$conexion = new mysqli($servidor, $usuario, $clave, $basededatos);

// Detiene la ejecución si la conexión no pudo establecerse.
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>