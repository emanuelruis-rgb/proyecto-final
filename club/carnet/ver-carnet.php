<?php
ob_start();
$cedula = $_GET['cedula'];

$carpetaCarnets = __DIR__ . "/carnets/";

// Busca el carnet del jugador usando su cédula y cualquier extensión
$archivos = glob($carpetaCarnets . $cedula . ".*");

// Si no se recibió una cédula o no se encontró el carnet, detiene el programa.
if (empty($cedula) || empty($archivos)) {
    echo "Este jugador no tiene un carnet subido.";
    exit;
}

// Guarda la ruta del primer archivo encontrado.
$archivo = $archivos[0];

// Detecta el tipo de contenido del archivo para indicárselo al navegador.
$finfo = new finfo(FILEINFO_MIME_TYPE);
$tipoArchivo = $finfo->file($archivo);

// Indica el tipo de contenido y que debe mostrarse en el navegador.
header("Content-Type: " . $tipoArchivo);
header("Content-Disposition: inline; filename=\"" . basename($archivo) . "\"");

// Envía el contenido del carnet para que el navegador lo muestre.
readfile($archivo);
exit;
?>
