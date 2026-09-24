<?php
session_name('club_session');
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto-final/conexion-bd/conexion.php";
$conexion = connection();

$nombreSesionClub = $_SESSION['nombre'] ?? '';
$nombreMostrarClub = $nombreSesionClub !== '' ? $nombreSesionClub : 'Club';

$stmt = mysqli_prepare($conexion, "SELECT nombreClub FROM club WHERE nombreClub = ?");
mysqli_stmt_bind_param($stmt, "s", $nombreSesionClub);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if ($fila = mysqli_fetch_assoc($resultado)) {
    $nombreMostrarClub = $fila['nombreClub'];
}

require_once __DIR__ . '/boletines-data.php';

// Carga los boletines para mostrar solo el resumen en la página principal.
$boletines = $conexion ? obtenerBoletines($conexion) : [];
if ($conexion) {
    // La conexión ya no se necesita después de cargar los datos.
    mysqli_close($conexion);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página principal clubes</title>
    <link rel="stylesheet" href="pagina-principal-club.css?v=6">
    <script src="menu-club.js" defer></script>
    <link rel="icon" type="image/png" href="../img/logo-liga/log-liga-b.png">
</head>
<body>
    <header> 
        <nav class="nav-izquierda-container">
            <a href="/proyecto-final/club/indexclub.php">
                <img src="/proyecto-final/img/logo-liga/log-liga-d.png" alt="Logo" class="logo-empresa">
            </a>
            <div class="nav-izquierda-botones-container">
                <!-- aca van los jugadores propios y despues da la opcion de seleccionar los ajenos-->
                <a href="jugadores-club.php" class="nav-izquierda-botones">Jugadores</a>
            </div>
        </nav>

        <!-- Identifica el espacio de trabajo sin añadir otra opción al navbar. -->
        <div class="club-header-context" aria-label="Sección actual">
            <span>Portal del club</span>
            <small>Gestión y novedades de la liga</small>
        </div>

        <div class="user-menu">
            <button class="user-menu-toggle" onclick="toggleUserMenu()">
                <i class="bi bi-person-circle"></i>
                <span class="user-menu-name"><?php echo htmlspecialchars($nombreMostrarClub); ?></span>
            </button>

            <div class="user-menu-dropdown" id="userDropdown">
                <a href="../uploads/documentos/reglamento.pdf" class="user-menu-item" download>
                    <i class="bi bi-file-earmark-text"></i> Descargar documento
            </a>
            <a href="/proyecto-final/index.php" class="user-menu-item">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar sesión</span>
            </a>
            </div>
        </div>
    </header>

    <div class="layout-abajo-header">
         <!-- Carrusel manual: el usuario cambia la imagen con los botones. -->
            <div class="carrusel-wrapper">
                <div class="carrusel">
                    <div class="slides">
                        <?php
                        // Busca imágenes válidas en la carpeta del carrusel.
                        $carpetaImg = "../img/carrusel/";
                        $imagenes = glob($carpetaImg . "*.{jpg,jpeg,png,webp,gif}", GLOB_BRACE);

                        // Informa si todavía no hay imágenes cargadas.
                        if (empty($imagenes)) {
                            echo "<p>No hay imágenes cargadas todavía.</p>";
                        }

                        // Genera una diapositiva por imagen encontrada.
                        foreach ($imagenes as $ruta) {
                            echo '<div class="slide-item">';
                            echo '  <img src="' . htmlspecialchars($ruta) . '" alt="Imagen carrusel">';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Permite avanzar o retroceder sin movimiento automático. -->
                <div class="controles-carrusel">
                    <button type="button" id="anteriorCarrusel" class="btn-carrusel">Anterior</button>
                    <span id="indicadorCarrusel">Imagen 1</span>
                    <button type="button" id="siguienteCarrusel" class="btn-carrusel">Siguiente</button>
                </div>

                <script>
                    // Control manual del carrusel: solo se muestra una imagen a la vez.
                    const diapositivas = document.querySelectorAll('.slide-item');
                    const indicador = document.getElementById('indicadorCarrusel');
                    let diapositivaActual = 0;

                    function mostrarDiapositiva(indice) {
                        if (diapositivas.length === 0) return;
                        diapositivaActual = (indice + diapositivas.length) % diapositivas.length;
                        diapositivas.forEach((diapositiva, posicion) => {
                            diapositiva.classList.toggle('activa', posicion === diapositivaActual);
                        });
                        indicador.textContent = 'Imagen ' + (diapositivaActual + 1) + ' de ' + diapositivas.length;
                    }

                    document.getElementById('anteriorCarrusel').addEventListener('click', function() {
                        mostrarDiapositiva(diapositivaActual - 1);
                    });

                    document.getElementById('siguienteCarrusel').addEventListener('click', function() {
                        mostrarDiapositiva(diapositivaActual + 1);
                    });

                    mostrarDiapositiva(0);
                </script>
            </div>
            <!-- TERMINA CARRUSEL -->

            <!-- Muestra una vista breve y enlaza con el listado completo. -->
            <section class="boletines-resumen" aria-labelledby="titulo-boletines">
                <div class="boletines-encabezado">
                    <div>
                        <p class="boletines-etiqueta">Información de la liga</p>
                        <h1 id="titulo-boletines">Últimos boletines</h1>
                    </div>
                    <a href="boletines-club.php" class="boletines-ver-todos">
                        Ver todos <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                <?php if (empty($boletines)): ?>
                    <!-- Estado visible cuando todavía no hay publicaciones. -->
                    <div class="boletines-vacio">
                        <i class="bi bi-newspaper" aria-hidden="true"></i>
                        <p>Aún no hay boletines publicados.</p>
                    </div>
                <?php else: ?>
                    <!-- En el inicio solo se muestran los tres más recientes. -->
                    <div class="boletines-grid">
                        <?php foreach (array_slice($boletines, 0, 3) as $boletin): ?>
                            <article class="boletin-item">
                                <i class="bi bi-megaphone boletin-icono" aria-hidden="true"></i>
                                <div>
                                    <p class="boletin-fecha">
                                        <?php echo htmlspecialchars(date('d/m/Y', strtotime($boletin['fechaSubida']))); ?>
                                    </p>
                                    <h2><?php echo htmlspecialchars($boletin['titulo']); ?></h2>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
        
    </div>
</body>
</html>