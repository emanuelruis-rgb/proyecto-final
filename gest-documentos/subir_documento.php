<?php
// Guarda el reglamento recibido como PDF con el nombre público esperado por los clubes.
$carpetaDestino = __DIR__ . '/../uploads/documentos/';

if (!is_dir($carpetaDestino)) {
    // Crea la carpeta de destino cuando aún no existe.
    mkdir($carpetaDestino, 0755, true);
}

$mensaje = 'error'; // valor por defecto

if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {

    // Extrae el archivo temporal y verifica su extensión antes de reemplazar el reglamento.
    $archivoTmp = $_FILES['pdf']['tmp_name'];
    $nombreOriginal = basename($_FILES['pdf']['name']);
    $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

    if ($extension === 'pdf') {
        $rutaFinal = $carpetaDestino . 'reglamento.pdf';

        if (move_uploaded_file($archivoTmp, $rutaFinal)) {
            $mensaje = 'exito';
        }
    } else {
        $mensaje = 'extension';
    }
}

// En vez de hacer echo, redirige de vuelta a documentos.php con el resultado
header('Location: documento.php?estado=' . $mensaje);
exit;