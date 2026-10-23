<?php
// Actualiza los datos del club, sus categorías y sus competiciones.
include(__DIR__ . "/../../conexion-bd/conexion.php");
$con = connection();

$id            = (int)$_POST["idClub"];
$nombre        = mysqli_real_escape_string($con, $_POST['nombreClub']);
$contraseña    = mysqli_real_escape_string($con, $_POST['contraseñaClub']);
$presidente    = mysqli_real_escape_string($con, $_POST['nombrePresidente']);
$año_fundacion = mysqli_real_escape_string($con, $_POST['añoCreacion']);
$estadio       = mysqli_real_escape_string($con, $_POST['estadio']);

// Si no se marca ninguna casilla, el campo no llega: en ese caso usamos una lista vacía.
$categorias    = $_POST['categorias'] ?? [];
$competiciones = $_POST['competiciones'] ?? [];

// 1) Datos del club
$sql = "UPDATE club SET nombreClub='$nombre', contraseñaClub='$contraseña', nombrePresidente='$presidente', añoCreacion='$año_fundacion', estadio='$estadio' WHERE idClub='$id'";
$query = mysqli_query($con, $sql);

// 2) Categorías: borra las que tenía y guarda las marcadas
mysqli_query($con, "DELETE FROM club_categoria WHERE idClub = $id");
foreach ($categorias as $idCategoria) {
    $idCategoria = (int)$idCategoria;
    mysqli_query($con, "INSERT INTO club_categoria (idClub, idCategoria) VALUES ($id, $idCategoria)");
}

// 3) Competiciones: borra las que tenía y guarda las marcadas
mysqli_query($con, "DELETE FROM club_competicion WHERE idClub = $id");
foreach ($competiciones as $idCompeticion) {
    $idCompeticion = (int)$idCompeticion;
    mysqli_query($con, "INSERT INTO club_competicion (idClub, idCompeticion) VALUES ($id, $idCompeticion)");
}

if ($query) {
    header("Location: club.php");
} else {
    echo "No se pudo actualizar el club.";
}
?>