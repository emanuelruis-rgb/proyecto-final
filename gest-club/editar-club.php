<?php

include(__DIR__ . "/../conexion-bd/conexion.php");
$con = connection();

$id=$_POST["idClub"];
$nombre = $_POST['nombreClub'];
$contraseña = $_POST['contraseñaClub'];
$presidente = $_POST['nombrePresidente'];
$año_fundacion = $_POST['añoCreacion'];
$estadio = $_POST['estadio'];

$sql="UPDATE club SET nombreClub='$nombre', contraseñaClub='$contraseña', nombrePresidente='$presidente', añoCreacion='$año_fundacion', estadio='$estadio' WHERE idClub='$id'";
$query = mysqli_query($con, $sql);

if($query){
    Header("Location: club.php");
}else{

}

?>