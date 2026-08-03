<?php
$carpeta = "../img/carrusel/";

if (isset($_GET["archivo"])) {
    // basename() evita que alguien borre archivos fuera de esta carpeta
    $archivo = basename($_GET["archivo"]);
    $ruta = $carpeta . $archivo;

    if (file_exists($ruta)) {
        unlink($ruta);
    }
}

header("Location: indexadmin.php");
exit;
