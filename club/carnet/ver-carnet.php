<?php
ob_start();
$cedula = $_GET['cedula'];

$carpetaCarnets = __DIR__ . "/carnets/";

// Busca el carnet del jugador usando su cédula y cualquier extensión
$archivos = glob($carpetaCarnets . $cedula . ".*");

// Si no se recibió una cédula o no se encontró el carnet, detiene el programa.
if (empty($cedula) || empty($archivos)) {
    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Carnet médico no disponible</title>
        <link rel="stylesheet" href="../jugador/jugadores-club-style.css">
        <link rel="stylesheet"
              href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    </head>
    <body>
        <main class="mensaje-carnet-pagina">
            <section class="mensaje-carnet-vacio" role="status">

                <h1>Carnet médico no disponible</h1>

                <p>
                    Este jugador todavía no tiene un carnet médico
                    cargado en el sistema.
                </p>

                <a href="../jugador/jugadores-club.php?tipoClub=propios"
                   class="mensaje-carnet-boton">
                    <i class="bi bi-arrow-left"></i>
                    Volver a jugadores
                </a>
            </section>
        </main>
    </body>
    </html>
    ';
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
