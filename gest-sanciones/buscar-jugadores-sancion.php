<?php
include(__DIR__ . "/../conexion-bd/conexion.php");
$conexion = connection();

/* este get del idclub es el que recibe el id club de sanciones.js
para ejecutar este archivo, se necesita primero el js, y ese js lo ejecuta en el fetch() mandando
el idclub sacado del select de clubes de sanciones.php */
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