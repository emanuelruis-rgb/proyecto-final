<?php
// Convierte la hora numérica de la BD (por ejemplo, 1500) al formato 15:00.
function formatoHora($hora) {
    return sprintf('%02d:%02d', intdiv((int)$hora, 100), (int)$hora % 100);
}

/* Ejecuta una consulta preparada y devuelve las filas (o [] si no devuelve filas, como un UPDATE).
   Ejemplo: consultar($con, "SELECT * FROM club WHERE idClub = ?", "i", $id)
   Los "?" se reemplazan de forma segura por los valores; $tipos indica el tipo de cada uno (i = entero, s = texto). */
function consultar($con, $sql, $tipos = "", ...$valores) {
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) { return []; }
    if ($tipos !== "") { mysqli_stmt_bind_param($stmt, $tipos, ...$valores); }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

// Devuelve los partidos agrupados por NÚMERO DE FECHA del campeonato: $fixture[1] = partidos de la Fecha 1.
// Un filtro en 0 trae todas las competiciones, categorías o fechas.
function obtenerFixture($con, $idCompeticion = 0, $idCategoria = 0, $numeroFecha = 0) {
    $filas = consultar($con,
        "SELECT p.idPartido, p.idCompeticion, p.jornada AS numeroFecha,
                IF(p.jugado = 1, 'jugado', 'pendiente') AS estado,
                p.jugado, p.fechaPartido, p.horaPartido, p.golesLocal, p.golesVisitante,
                cl.nombreClub AS local, cv.nombreClub AS visitante
         FROM partido p
         JOIN club cl ON p.idClubLocal = cl.idClub
         JOIN club cv ON p.idClubVisitante = cv.idClub
         WHERE (? = 0 OR p.idCompeticion = ?) AND (? = 0 OR p.idCategoria = ?) AND (? = 0 OR p.jornada = ?)
         ORDER BY p.jornada, p.fechaPartido, p.horaPartido",
        "iiiiii", $idCompeticion, $idCompeticion, $idCategoria, $idCategoria, $numeroFecha, $numeroFecha);

    $fixture = [];
    foreach ($filas as $fila) {
        $fixture[$fila['numeroFecha']][] = $fila;
    }
    return $fixture;
}

// Números de fecha que ya tienen partidos cargados: [1, 2, 3...] (para el filtro).
function obtenerNumerosFecha($con) {
    return array_map('intval', array_column(consultar($con, "SELECT DISTINCT jornada FROM partido ORDER BY jornada"), 'jornada'));
}

// Lista de clubes (para llenar los <select> del formulario).
function obtenerClubes($con) {
    return consultar($con, "SELECT idClub, nombreClub FROM club ORDER BY nombreClub");
}

// Texto de la columna "Hora / resultado" según el estado del partido.
function textoResultado($partido) {
    if ($partido['estado'] === 'jugado') {
        return (int)$partido['golesLocal'] . ' - ' . (int)$partido['golesVisitante'];
    }
    return ['pospuesto' => 'Pospuesto', 'suspendido' => 'Suspendido'][$partido['estado']] ?? formatoHora($partido['horaPartido']);
}

// ¿Alguno de los dos clubes ya juega en esa fecha del campeonato (misma competición y categoría)?
// Los clubes se pasan dos veces porque aparecen en dos IN (como local y como visitante).
function clubYaJuegaEnFecha($con, $d) {
    return (bool)consultar($con,
        "SELECT 1 FROM partido
         WHERE jornada = ? AND idCompeticion = ? AND idCategoria = ?
           AND (idClubLocal IN (?, ?) OR idClubVisitante IN (?, ?)) LIMIT 1",
        "iiiiiii", $d['numeroFecha'], $d['idCompeticion'], $d['idCategoria'],
        $d['idClubLocal'], $d['idClubVisitante'], $d['idClubLocal'], $d['idClubVisitante']);
}

// Inserta un partido nuevo. Devuelve true si salió bien.
function crearPartido($con, $d) {
    $stmt = mysqli_prepare($con,
        "INSERT INTO partido (idClubLocal, idClubVisitante, golesLocal, golesVisitante, duracionPartido, arbitro,
                              estadio, fechaPartido, horaPartido, idCompeticion, idCategoria, jugado, jornada)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iiiiisssiiiii", $d['idClubLocal'], $d['idClubVisitante'], $d['golesLocal'],
        $d['golesVisitante'], $d['duracionPartido'], $d['arbitro'], $d['estadio'], $d['fechaPartido'],
        $d['horaPartido'], $d['idCompeticion'], $d['idCategoria'], $d['jugado'], $d['numeroFecha']);
    return mysqli_stmt_execute($stmt);
}

// Elimina un partido por su ID. Devuelve true si salió bien.
function eliminarPartido($con, $idPartido) {
    $stmt = mysqli_prepare($con, "DELETE FROM partido WHERE idPartido = ?");
    mysqli_stmt_bind_param($stmt, "i", $idPartido);
    return mysqli_stmt_execute($stmt);
}

// Guarda el estado y el marcador de un partido. Devuelve true si salió bien.
function guardarPartido($con, $idPartido, $estado, $golesLocal, $golesVisitante) {
    if (!in_array($estado, ['pendiente', 'jugado'], true) || $idPartido < 1) {
        return false;
    }

    // Solo un partido jugado tiene marcador
    if ($estado !== 'jugado') {
        $golesLocal = $golesVisitante = 0;
    } else {
        $golesLocal = min(30, max(0, (int)$golesLocal));
        $golesVisitante = min(30, max(0, (int)$golesVisitante));
    }

    // La columna jugado se mantiene porque la tabla de posiciones la usa
    $jugado = (int)($estado === 'jugado');

    $stmt = mysqli_prepare($con, "UPDATE partido SET jugado = ?, golesLocal = ?, golesVisitante = ? WHERE idPartido = ?");
    if (!$stmt) {
        return false;
    }
    mysqli_stmt_bind_param($stmt, "iiii", $jugado, $golesLocal, $golesVisitante, $idPartido);
    return mysqli_stmt_execute($stmt);
}