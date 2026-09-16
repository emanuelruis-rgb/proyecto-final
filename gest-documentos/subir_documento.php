<?php
// Carpeta donde se guardan los PDFs
$carpetaDestino = __DIR__ . '/../uploads/documentos/';

// Crear la carpeta si no existe
if (!is_dir($carpetaDestino)) {
    mkdir($carpetaDestino, 0777, true); // da permisos de lectura, escritura y ejecucion, ademas de crear la carpeta si no existe
}
 // ve si se subio un archivo y si no hubo errores
if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {

    $archivoTmp = $_FILES['pdf']['tmp_name']; // donde se guarda el archivo temporalmente
    $nombreOriginal = basename($_FILES['pdf']['name']); // nombre original del archivo
    $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION)); // extrae la extension del archivo y la deja en minuscula

    // valida que sea PDF
    if ($extension !== 'pdf') {
        header('Location: documento.php?error=1');
        exit;
    }
// se fija el nombre como reglamento.pdf para que siempre se guarde con ese nombre y no se sobreescriba
    $nombreFinal = 'reglamento.pdf';
    $rutaFinal = $carpetaDestino . $nombreFinal;

    //mueve el archivo temporal a la carpeta de destino con el nombre final
    if (move_uploaded_file($archivoTmp, $rutaFinal)) {
        header('Location: documento.php?ok=1');
        exit;
    }

} else {
    echo "No se seleccionó ningún archivo o hubo un error.";
}

header('Location: documento.php?error=1');
exit;
?>