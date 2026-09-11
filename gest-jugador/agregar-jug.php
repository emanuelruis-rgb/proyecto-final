<?php
include(__DIR__ . "/../conexion-bd/conexion.php");
$con = connection();

$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$cedula = $_POST['cedula'] ?? '';
$idClub = $_POST['idclub'] ?? '';
/* aca se pasa a un date time immutable, un objeto fecha, y se formatea pasandolo a string */
$fechaNacimientoRaw = $_POST['fecha-nacimiento'] ?? '';
$fechaNacimiento = new DateTimeImmutable($fechaNacimientoRaw);
$fechaNacimiento = $fechaNacimiento->format('Y-m-d');

$genero = $_POST['genero'] ?? '';
$idCategoria = $_POST['idcategoria'] ?? '';

$sql = "INSERT INTO jugador (nombre, apellido, cedula, idClub, fechaNacimiento, genero, idCategoria)
        VALUES ('$nombre', '$apellido', '$cedula', '$idClub', '$fechaNacimiento', '$genero', '$idCategoria')";
$query = mysqli_query($con, $sql);

if($query){
    Header("Location: jugador.php");
}else{
    die("Error al registrar el jugador: " . mysqli_error($con));
}

?>