<?php
// Devuelve los partidos agrupados por jornada, listos para pintar.
function obtenerFixture($con) {
    $sql = "SELECT p.idPartido, p.jornada, p.horaPartido,
                   p.golesLocal, p.golesVisitante,
                   cl.nombreClub AS local, cv.nombreClub AS visitante
            FROM partido p
            JOIN club cl ON p.idClubLocal = cl.idClub
            JOIN club cv ON p.idClubVisitante = cv.idClub
            ORDER BY p.jornada, p.fechaPartido, p.horaPartido";

    $result = mysqli_query($con, $sql);
    $fixture = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $fixture[$row['jornada']][] = $row;
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
                (idClubLocal, idClubVisitante, fechaPartido, horaPartido, 
                 estadio, arbitro, jornada)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        "iisisis",
        $datos['idClubLocal'],
        $datos['idClubVisitante'],
        $datos['fechaPartido'],
        $datos['horaPartido'],
        $datos['estadio'],
        $datos['arbitro'],
        $datos['jornada']
    );

    return mysqli_stmt_execute($stmt);
}

// Elimina un partido por su ID. Devuelve true si salió bien.
function eliminarPartido($con, $idPartido) {
    $sql = "DELETE FROM partido WHERE idPartido = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idPartido);
    return mysqli_stmt_execute($stmt);
}
