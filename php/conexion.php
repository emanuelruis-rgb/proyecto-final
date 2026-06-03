<?php
$servidor = "localhost"; 
$usuario = "root";       
$clave = "hola123@";  
$basededatos = "liga_de_futbol"; 

$conexion = new mysqli($servidor, $usuario, $clave, $basededatos);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
} else {
    echo "Conexión exitosa a la base de datos";
}
?>