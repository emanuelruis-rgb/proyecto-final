<?php
include(__DIR__ . "/../../conexion-bd/conexion.php");
$conexion = connection();

/* Datos enviados desde sanciones.php */
$clubSancion      = (int)($_POST["id-club-sancion"] ?? 0);
$cedulaJugador    = $_POST["ci-jugador-sancion"] ?? '';
$tipoSancion      = $_POST["tipo-sancion"] ?? '';
$motivoSancion    = $_POST["motivo-sancion"] ?? '';
$fechasSuspension = (int)($_POST["numero-fechas"] ?? 0);

// Si faltan datos o el tipo no es válido, vuelve sin guardar nada
if ($cedulaJugador === '' || !in_array($tipoSancion, ['Amarilla', 'Roja', 'Disciplinaria'], true)) {
    header("Location: sanciones.php");
    exit();
}

// La amarilla nunca lleva fechas de suspensión
if ($tipoSancion === 'Amarilla') {
    $fechasSuspension = 0;
}

/* Consulta preparada: cada "?" se reemplaza de forma segura por el valor,
   así un motivo con comillas no rompe la consulta ni permite inyección SQL.
   "sssii" = tres textos y dos enteros, en el orden de los "?". */
$stmt = mysqli_prepare($conexion,
    "INSERT INTO sancion (tipo, cedulaJugador, motivo, fechaSuspencion, clubSancion) VALUES (?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sssii", $tipoSancion, $cedulaJugador, $motivoSancion, $fechasSuspension, $clubSancion);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Control de acumulación: cada 3 amarillas del mismo jugador genera una roja automática
$amarillasParaRoja = 3;
$fechasRoja = 1;   // fechas de suspensión de la roja automática (poné las que diga tu reglamento)

if ($tipoSancion === 'Amarilla') {
    // Cuenta todas las amarillas del jugador, incluida la que se acaba de guardar
    $stmt = mysqli_prepare($conexion, "SELECT COUNT(*) FROM sancion WHERE cedulaJugador = ? AND tipo = 'Amarilla'");
    mysqli_stmt_bind_param($stmt, "s", $cedulaJugador);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $totalAmarillas);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // % devuelve el resto de la división: es 0 cuando el total es 3, 6, 9...
    if ($totalAmarillas % $amarillasParaRoja === 0) {
        $tipoRoja   = 'Roja';
        $motivoRoja = "Acumulación de $totalAmarillas amarillas";
        $stmt = mysqli_prepare($conexion,
            "INSERT INTO sancion (tipo, cedulaJugador, motivo, fechaSuspencion, clubSancion) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssii", $tipoRoja, $cedulaJugador, $motivoRoja, $fechasRoja, $clubSancion);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

header("Location: sanciones.php");
exit();