<?php
// Carpeta donde se guardan las imágenes del carrusel
$carpetaDestino = "../img/carrusel/";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["nuevaImagen"])) {

    $extensionesPermitidas = ["jpg", "jpeg", "png", "webp", "gif"];
    $archivos = $_FILES["nuevaImagen"];

    // El input tiene "multiple", así que puede venir 1 o varios archivos
    $cantidad = is_array($archivos["name"]) ? count($archivos["name"]) : 1;

    for ($i = 0; $i < $cantidad; $i++) {
        $nombreOriginal = is_array($archivos["name"]) ? $archivos["name"][$i] : $archivos["name"];
        $tmpName        = is_array($archivos["tmp_name"]) ? $archivos["tmp_name"][$i] : $archivos["tmp_name"];
        $error          = is_array($archivos["error"]) ? $archivos["error"][$i] : $archivos["error"];

        if ($error !== UPLOAD_ERR_OK) continue;

        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
        if (!in_array($extension, $extensionesPermitidas)) continue;

        // Nombre único para que no se pisen imágenes entre sí
        $nombreFinal = uniqid("img_", true) . "." . $extension;
        $rutaDestino = $carpetaDestino . $nombreFinal;

        move_uploaded_file($tmpName, $rutaDestino);
    }
}

header("Location: indexadmin.php");
exit;
