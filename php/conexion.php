<?php
$servidor = "localhost"; 
$usuario = "root";       
$clave = "";  
$basededatos = "liga"; 

$conexion = new mysqli($servidor, $usuario, $clave, $basededatos);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
} else {
    echo "Conexión exitosa a la base de datos";
}
?>