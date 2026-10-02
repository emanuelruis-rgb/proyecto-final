<?php
// Convierte la hora numérica de la BD (por ejemplo, 1500) al formato 15:00.
function formatoHora($hora) {
    $hora = (int)$hora;
    $horas = intdiv($hora, 100);
    $minutos = $hora % 100;

    return sprintf('%02d:%02d', $horas, $minutos);
}

// Devuelve los partidos agrupados por fecha.
// Si $idCompeticion es 0 trae todas las competiciones; si no, solo la indicada.
function obtenerFixture($con, $idCompeticion = 0) {
    $sql = "SELECT p.idPartido,
                   p.idCompeticion,
                   p.jugado,
                   p.fechaPartido,
                   p.horaPartido,
                   p.golesLocal,
                   p.golesVisitante,
                   cl.nombreClub AS local,
                   cv.nombreClub AS visitante
            FROM partido p
            JOIN club cl ON p.idClubLocal = cl.idClub
            JOIN club cv ON p.idClubVisitante = cv.idClub
            WHERE (? = 0 OR p.idCompeticion = ?)
            ORDER BY p.fechaPartido, p.horaPartido";

    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        return [];
    }

    mysqli_stmt_bind_param($stmt, "ii", $idCompeticion, $idCompeticion);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        return [];
    }

    $fixture = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $fixture[$row['fechaPartido']][] = $row;
    }

    return $fixture;
}

// Devuelve la lista de clubes (para llenar los <select> del formulario).
function obtenerClubes($con) {
    $sql = "SELECT idClub, nombreClub FROM club ORDER BY nombreClub";
    $result = mysqli_query($con, $sql);
    $clubes = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $clubes[] = $row;
    }

    return $clubes;
}

// Inserta un partido nuevo. Devuelve true si salió bien.
function crearPartido($con, $datos) {
    $sql = "INSERT INTO partido
                (idClubLocal, idClubVisitante, golesLocal, golesVisitante,
                 duracionPartido, arbitro, estadio, fechaPartido, horaPartido,
                 idCompeticion, jugado)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        "iiiiisssiii",
        $datos['idClubLocal'],
        $datos['idClubVisitante'],
        $datos['golesLocal'],
        $datos['golesVisitante'],
        $datos['duracionPartido'],
        $datos['arbitro'],
        $datos['estadio'],
        $datos['fechaPartido'],
        $datos['horaPartido'],
        $datos['idCompeticion'],
        $datos['jugado']
    );

    return mysqli_stmt_execute($stmt);
}

// Guarda el marcador y marca el partido como jugado para incluirlo en posiciones.
function registrarResultado($con, $idPartido, $golesLocal, $golesVisitante) {
    $sql = "UPDATE partido
            SET golesLocal = ?, golesVisitante = ?, jugado = 1
            WHERE idPartido = ? AND jugado = 0";

    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "iii", $golesLocal, $golesVisitante, $idPartido);
    $actualizado = mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) === 1;
    mysqli_stmt_close($stmt);

    return $actualizado;
}

// Elimina un partido por su ID. Devuelve true si salió bien.
function eliminarPartido($con, $idPartido) {
    $sql = "DELETE FROM partido WHERE idPartido = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idPartido);
    return mysqli_stmt_execute($stmt);
}