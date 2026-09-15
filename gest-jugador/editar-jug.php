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
// Se reciben los nuevos valores físicos para actualizar la ficha del jugador.
$masa = $_POST['masa'];
$altura = $_POST['altura'];
$velocidad = $_POST['velocidad'];

// Se actualizan las magnitudes físicas junto con los datos existentes.
$sql = "UPDATE jugador SET 
        nombre='$nombre',
        apellido='$apellido',
        idClub='$idClub',
        fechaNacimiento='$fechaNacimiento',
        genero='$genero',
        idCategoria='$idCategoria',
        masa='$masa',
        altura='$altura',
        velocidad='$velocidad'
        WHERE cedula='$cedula'";

$query = mysqli_query($con, $sql);

if ($query) {
    header("Location: /proyecto-final/gest-jugador/jugador.php");
    exit();
} else {
    echo "Error al actualizar el jugador: " . mysqli_error($con);
}

?>