<?php

include(__DIR__ . "/../conexion-bd/conexion.php");
$con = connection();

$id=$_GET["id"];

$sql="DELETE FROM club WHERE idClub='$id'";
$query = mysqli_query($con, $sql);

if($query){
    Header("Location: club.php");
}else{

}

?>