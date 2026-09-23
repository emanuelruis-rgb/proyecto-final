<?php
$carpeta = __DIR__ . '/../uploads/documentos/';
$archivo = $carpeta . 'reglamento.pdf';
$existe = file_exists($archivo);

// Traduce el parámetro de la URL a un mensaje legible
$mensajes = [
    'exito'     => ['texto' => 'Archivo subido correctamente.', 'tipo' => 'exito'],
    'error'     => ['texto' => 'Hubo un error al subir el archivo.', 'tipo' => 'error'],
    'extension' => ['texto' => 'Solo se permiten archivos PDF.', 'tipo' => 'error'],
];

$estado = $_GET['estado'] ?? null;
$mensajeAMostrar = $mensajes[$estado] ?? null;

session_name('admin_session');
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto-final/conexion-bd/conexion.php";
$conexion = connection();

$nombreSesion = $_SESSION['nombre'];
$nombreMostrar = $nombreSesion; // valor por defecto, por si la consulta no encuentra nada

$stmt = mysqli_prepare($conexion, "SELECT nombreUsuario FROM administrador WHERE nombreUsuario = ?");
mysqli_stmt_bind_param($stmt, "s", $nombreSesion);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if ($fila = mysqli_fetch_assoc($resultado)) {
    $nombreMostrar = $fila['nombreUsuario'];
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
            </div>
        </nav>

        <script>
        function toggleUserMenu() {
        document.getElementById('userDropdown').classList.toggle('show'); //busca el elemento con la clase UserDropdown y activa/desactiva el menu
        }

        document.addEventListener('click', function(event) {
        const menu = document.querySelector('.user-menu');
        const dropdown = document.getElementById('userDropdown');
        if (!menu.contains(event.target)) {
            dropdown.classList.remove('show');
            }
        });
        </script>

        <div class="user-menu">
            <button class="user-menu-toggle" onclick="toggleUserMenu()">
                <i class="bi bi-person-circle"></i>
                <span class="user-menu-name"><?php echo htmlspecialchars($nombreMostrar); ?></span>
            </button>

            <div class="user-menu-dropdown" id="userDropdown">
                <a href="../gest-documentos/documento.php" class="user-menu-item">
                    <i class="bi bi-file-earmark-text"></i> Subir documento
            </a>
            <a href="/proyecto-final/index.php" class="user-menu-item">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar sesión</span>
            </a>
            </div>
        </div>
    </header>

    <div class="documentos-card">
        <?php if ($mensajeAMostrar): ?>
            <p class="mensaje-<?= $mensajeAMostrar['tipo'] ?>">
             <?= htmlspecialchars($mensajeAMostrar['texto']) ?>
             </p>
        <?php endif; ?>

        <form action="subir_documento.php" method="POST" enctype="multipart/form-data" class="documentos-form">
            <label for="pdf" class="documentos-label">Subir reglamento / documento (PDF):</label>
            <input type="file" name="pdf" id="pdf" accept="application/pdf" required class="documentos-input">
            <button type="submit" class="documentos-btn">Subir</button>
        </form>
    </div>
<!-- sirve para que el mensaje no se muestre aunque se recargue la pagina -->
    <script>
    if (window.location.search.includes('estado=')) {
        window.history.replaceState(null, '', window.location.pathname);
    }
    </script>
</body>
</html>