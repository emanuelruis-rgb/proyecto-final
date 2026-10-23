<?php
// Alterna el estado activo del club desde una solicitud POST del administrador.
session_name('admin_session');
session_start();
if (!isset($_SESSION['nombre'])) { header('Location: /proyecto-final/index.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: club.php'); exit; }

require_once __DIR__ . "/../../conexion-bd/conexion.php";
$con = connection();

$id = (int)($_POST['id'] ?? 0);
$stmt = mysqli_prepare($con, "UPDATE club SET activo = 1 - activo WHERE idClub = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

header('Location: club.php');
exit;