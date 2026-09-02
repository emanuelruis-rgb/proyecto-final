<?php
include(__DIR__ . "/../conexion-bd/conexion.php");
$conexion = connection();

$idClub = $_GET['idClub'];

/* esta query busca la ci, nombre y apellido de los jugadores que tengan el idclub recibido */
$queryJugadores = mysqli_query($conexion, "SELECT cedula, nombre, apellido FROM jugador WHERE idClub = $idClub");

/* este while busca jugador por jugador y ese echo arma el option para html con los datos de cada jugador*/
while ($jugador = mysqli_fetch_array($queryJugadores)) {
    echo '<option value="' . $jugador['cedula'] . '">'
        . $jugador['nombre'] . ' ' . $jugador['apellido']
        . ' - CI ' . $jugador['cedula']
        . '</option>';
}
?>