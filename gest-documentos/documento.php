<?php
$carpeta = __DIR__ . '/../uploads/documentos/';
$archivo = $carpeta . 'reglamento.pdf';
$existe = file_exists($archivo);
$mensaje = '';

if (isset($_GET['ok']) && $_GET['ok'] === '1') {
    $mensaje = 'El reglamento se subió correctamente.';
} elseif (isset($_GET['error']) && $_GET['error'] === '1') {
    $mensaje = 'No se pudo subir el archivo. Debe ser un PDF válido.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <title>Gestión de documentos</title>
    <link rel="stylesheet" href="/proyecto-final/admin/pagina-principal-admin.css">
    <link rel="icon" type="image/png" href="../img/logo-liga/log-liga-b.png">
</head>
<body>
    <header>
        <nav class="nav-izquierda-container">
            <a href="/proyecto-final/admin/indexadmin.php">
                <img src="/proyecto-final/img/logo-liga/log-liga-b.png" alt="Logo" class="logo-liga">
            </a>
            <div class="nav-izquierda-botones-container">
                <a href="/proyecto-final/gest-jugador/jugador.php" class="nav-izquierda-botones">Jugadores</a>
                <a href="/proyecto-final/gest-club/club.php" class="nav-izquierda-botones">Clubes</a>
                <a href="/proyecto-final/gest-fixture/fixtures.php" class="nav-izquierda-botones">Fixture</a>
                <a href="/proyecto-final/gest-sanciones/sanciones.php" class="nav-izquierda-botones">Sanciones</a>
                <a href="/proyecto-final/gest-documentos/documento.php" class="nav-izquierda-botones">Documentos</a>
            </div>
        </nav>
    </header>

    <div class="documentos-card">
        <!-- confirma si el mensaje no esta vacio y si no lo esta lo muestra en un div con la clase documentos-mensaje -->
        <?php if ($mensaje !== ''): ?>
            <div class="documentos-mensaje"><?php echo htmlspecialchars($mensaje); ?></div> <!-- sirve por si el mensaje tiene caracteres especiales -->git 
        <?php endif; ?>

        <form action="/proyecto-final/gest-documentos/subir_documento.php" method="POST" enctype="multipart/form-data" class="documentos-form">
            <label for="pdf" class="documentos-label">Subir formulario de los datos del partido (PDF)</label>
            <input type="file" name="pdf" id="pdf" accept="application/pdf" required class="documentos-input">
            <button type="submit" class="documentos-btn">Subir</button>
        </form>
    </div>
</body>
</html>