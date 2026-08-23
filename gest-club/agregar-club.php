<?php
include(__DIR__ . "/../conexion-bd/conexion.php");
$con = connection();

$nombre = $_POST['nombre'] ?? '';
$contraseña = $_POST['contraseña'] ?? '';
$presidente = $_POST['presidente'] ?? '';
$año_fundacion = $_POST['año-fundacion'] ?? '';
$estadio = $_POST['estadio'] ?? '';

$sql = "INSERT INTO club VALUES(NULL,'$nombre','$contraseña','$presidente','$año_fundacion','$estadio')";
$query = mysqli_query($con, $sql);

if($query){
    Header("Location: club.php");
}else{

}

?>