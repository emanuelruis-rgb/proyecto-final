<?php

include(__DIR__ . "/../conexion-bd/conexion.php");
$con = connection();

$cedula = $_POST['cedula'];
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$idClub = $_POST['idclub'];
$fechaNacimiento = $_POST['fechaNacimiento'];
$genero = $_POST['genero'];
$idCategoria = $_POST['idcategoria'];

$sql = "UPDATE jugador SET 
        nombre='$nombre',
        apellido='$apellido',
        idClub='$idClub',
        fechaNacimiento='$fechaNacimiento',
        genero='$genero',
        idCategoria='$idCategoria'
        WHERE cedula='$cedula'";

$query = mysqli_query($con, $sql);

if ($query) {
    header("Location: /proyecto-final/gest-jugador/jugador.php");
    exit();
} else {
    echo "Error al actualizar el jugador: " . mysqli_error($con);
}

?>