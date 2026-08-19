<?php
// Define la carpeta donde se guardan las imágenes del carrusel.
$carpetaDestino = "../img/carrusel/";

// Procesa únicamente solicitudes POST que incluyan archivos.
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["nuevaImagen"])) {

    // Limita la carga a formatos de imagen permitidos.
    $extensionesPermitidas = ["jpg", "jpeg", "png", "webp", "gif"];
    $archivos = $_FILES["nuevaImagen"];

    // Calcula cuántos archivos fueron seleccionados.
    $cantidad = is_array($archivos["name"]) ? count($archivos["name"]) : 1;

    for ($i = 0; $i < $cantidad; $i++) {
        // Obtiene los datos del archivo actual, sea único o múltiple.
        $nombreOriginal = is_array($archivos["name"]) ? $archivos["name"][$i] : $archivos["name"];
        $tmpName        = is_array($archivos["tmp_name"]) ? $archivos["tmp_name"][$i] : $archivos["tmp_name"];
        $error          = is_array($archivos["error"]) ? $archivos["error"][$i] : $archivos["error"];

        // Ignora archivos que terminaron con error durante la carga.
        if ($error !== UPLOAD_ERR_OK) continue;

        // Ignora extensiones que no estén autorizadas.
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
        if (!in_array($extension, $extensionesPermitidas)) continue;

        // Genera un nombre único para evitar reemplazar otras imágenes.
        $nombreFinal = uniqid("img_", true) . "." . $extension;
        $rutaDestino = $carpetaDestino . $nombreFinal;

        // Mueve el archivo temporal a la carpeta definitiva.
        move_uploaded_file($tmpName, $rutaDestino);
    }
}

// Regresa al panel de administración después de procesar la carga.
header("Location: indexadmin.php");
exit;
