<?php

include(__DIR__ . "/../conexion-bd/conexion.php");
$con = connection();

$id=$_GET["id"];

$sql="DELETE FROM jugador WHERE cedula='$id'";
$query = mysqli_query($con, $sql);

if($query){
    Header("Location: jugador.php");
}else{

}

?>