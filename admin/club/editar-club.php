<?php
// Actualiza los datos del club, sus categorías y sus competiciones.
include(__DIR__ . "/../../conexion-bd/conexion.php");
$con = connection();
$carpeta = __DIR__ . '/logos/';

$id = (int)$_POST["idClub"];

// Logo actual del club (se conserva si no se sube uno nuevo)
$stmt = mysqli_prepare($con, "SELECT logoClub FROM club WHERE idClub = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$resultado  = mysqli_stmt_get_result($stmt);
$clubActual = mysqli_fetch_assoc($resultado);

$rutaLogo     = $clubActual['logoClub'] ?? null;
$logoAnterior = $rutaLogo;

// Si se eligió un logo nuevo, se valida, se guarda y reemplaza al anterior.
if (isset($_FILES['logoClub']) && $_FILES['logoClub']['error'] !== UPLOAD_ERR_NO_FILE) {
    $logo = $_FILES['logoClub'];

    if ($logo['error'] !== UPLOAD_ERR_OK) {
        die("Error al subir el logo.");
    }
    if ($logo['size'] > 2 * 1024 * 1024) {
        die("El logo no puede superar los 2 MB.");
    }

    $tiposPermitidos = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/webp' => 'webp'];
    $mime = mime_content_type($logo['tmp_name']);
    if (!isset($tiposPermitidos[$mime])) {
        die("El logo debe ser una imagen PNG, JPG o WEBP.");
    }

    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0755, true);
    }

    $archivo = bin2hex(random_bytes(8)) . '.' . $tiposPermitidos[$mime];
    if (!move_uploaded_file($logo['tmp_name'], $carpeta . $archivo)) {
        die("No se pudo guardar el logo en el servidor.");
    }

    $rutaLogo = 'admin/club/logos/' . $archivo;
}

$nombre        = mysqli_real_escape_string($con, $_POST['nombreClub']);
$contraseña    = mysqli_real_escape_string($con, $_POST['contraseñaClub']);
$presidente    = mysqli_real_escape_string($con, $_POST['nombrePresidente']);
$año_fundacion = mysqli_real_escape_string($con, $_POST['añoCreacion']);
$estadio       = mysqli_real_escape_string($con, $_POST['estadio']);

// Si no se marca ninguna casilla, el campo no llega: en ese caso usamos una lista vacía.
$categorias    = $_POST['categorias'] ?? [];
$competiciones = $_POST['competiciones'] ?? [];

// 1) Datos del club
$sql = "UPDATE club SET
        nombreClub='$nombre',
        contraseñaClub='$contraseña',
        nombrePresidente='$presidente',
        añoCreacion='$año_fundacion',
        estadio='$estadio',
        logoClub='" . mysqli_real_escape_string($con, $rutaLogo ?? '') . "'
        WHERE idClub='$id'";
$query = mysqli_query($con, $sql);

// Si se subió un logo nuevo y la base se actualizó, borra el archivo anterior
if ($query && $logoAnterior && $rutaLogo !== $logoAnterior) {
    $archivoAnterior = $carpeta . basename($logoAnterior);
    if (file_exists($archivoAnterior)) {
        unlink($archivoAnterior);
    }
}

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