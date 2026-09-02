<?php
    include(__DIR__ . "/../conexion-bd/conexion.php");
    $conexion = connection();
    
    /* recibe aca los datos mandados de sanciones.php */
    $clubSancion = $_POST["id-club-sancion"];
    $cedulaJugador = $_POST["ci-jugador-sancion"];
    $tipoSancion = $_POST["tipo-sancion"];
    $motivoSancion = $_POST["motivo-sancion"];
    $fechasSuspension = $_POST["numero-fechas"];
?>