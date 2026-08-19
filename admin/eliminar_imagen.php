<?php
// Define la carpeta que contiene las imágenes del carrusel.
$carpeta = "../img/carrusel/";

// Elimina la imagen solicitada, si se recibió un nombre de archivo.
if (isset($_GET["archivo"])) {
    // basename() evita rutas que intenten salir de la carpeta permitida.
    $archivo = basename($_GET["archivo"]);
    $ruta = $carpeta . $archivo;

    // Borra el archivo solo si existe físicamente.
    if (file_exists($ruta)) {
        unlink($ruta);
    }
}

// Regresa al panel después de completar la operación.
header("Location: indexadmin.php");
exit;
