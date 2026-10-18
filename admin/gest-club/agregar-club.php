<?php
session_name('admin_session');
session_start();
if (!isset($_SESSION['nombre'])) { header('Location: /proyecto-final/index.php'); exit; }

require_once __DIR__ . "/../../conexion-bd/conexion.php";
$con = connection();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // los errores de MySQL lanzan excepción

$nombre     = $_POST['nombre'] ?? '';
$clave      = $_POST['contraseña'] ?? '';
$presidente = $_POST['presidente'] ?? '';
$anio       = (int)($_POST['año-fundacion'] ?? 0);
$estadio    = $_POST['estadio'] ?? '';

// Tablas intermedias: tabla => [columna, ids marcados en el formulario]
$relaciones = [
    'club_categoria'   => ['idCategoria',   array_map('intval', $_POST['categorias'] ?? [])],
    'club_competicion' => ['idCompeticion', array_map('intval', $_POST['competiciones'] ?? [])],
];

mysqli_begin_transaction($con);
try {
    $stmt = mysqli_prepare($con, "INSERT INTO club (nombreClub, contraseñaClub, nombrePresidente, añoCreacion, estadio)
        VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssis", $nombre, $clave, $presidente, $anio, $estadio);
    mysqli_stmt_execute($stmt);
    $idClub = mysqli_insert_id($con);

    foreach ($relaciones as $tabla => [$columna, $ids]) {
        $stmt = mysqli_prepare($con, "INSERT INTO $tabla (idClub, $columna) VALUES (?, ?)");
        foreach ($ids as $idRelacion) {
            mysqli_stmt_bind_param($stmt, "ii", $idClub, $idRelacion);
            mysqli_stmt_execute($stmt);
        }
    }

    mysqli_commit($con);
    header('Location: club.php');
    exit;
} catch (Throwable $e) {
    mysqli_rollback($con);
    die("Error al registrar el club: " . $e->getMessage());
}