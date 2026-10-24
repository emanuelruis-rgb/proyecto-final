<?php
// Restringe el alta de clubes al panel de administración autenticado.
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

// Logo (opcional): se valida, se guarda en /logos y se registra la ruta relativa.
$carpeta  = __DIR__ . '/logos/';
$rutaLogo = null;

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

    if (!is_dir($carpeta)) { // crea la carpeta si no existe
        mkdir($carpeta, 0755, true);
    }

    // genera nombre aleatorio para evitar colisiones y problemas de seguridad
    $archivo = bin2hex(random_bytes(8)) . '.' . $tiposPermitidos[$mime]; //genera 8 bytes aleatorios, los para a caracteres hexadecimales y le agrega la extensión correspondiente
    if (!move_uploaded_file($logo['tmp_name'], $carpeta . $archivo)) { // mueve el archivo subido a la carpeta de logos
        die("No se pudo guardar el logo en el servidor.");
    }

    $rutaLogo = 'admin/club/logos/' . $archivo;
}

// Registra el club y sus asociaciones como una única operación.
mysqli_begin_transaction($con);
try {
    $stmt = mysqli_prepare($con, "INSERT INTO club (nombreClub, contraseñaClub, nombrePresidente, añoCreacion, estadio, logoClub)
        VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssiss", $nombre, $clave, $presidente, $anio, $estadio, $rutaLogo);
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
    // Si falló la base de datos, borra el logo que ya se había subido
    if ($rutaLogo && file_exists($carpeta . basename($rutaLogo))) {
        unlink($carpeta . basename($rutaLogo));
    }
    die("Error al registrar el club: " . $e->getMessage());
}