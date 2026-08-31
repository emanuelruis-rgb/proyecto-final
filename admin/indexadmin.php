<?php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Carga Bootstrap, iconos, metadatos y estilos del panel. -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página principal admins</title>
    <link rel="stylesheet" href="pagina-principal-admin.css">
    <link rel="icon" type="image/png" href="../img/copa.png">
</head>
<body>
    <header> 
        <nav class="nav-izquierda-container">
            <a href="/proyecto-final/admin/indexadmin.php">
                <img src="/proyecto-final/img/logo-empresa/logo-empresa-blanco.png" alt="Logo" class="logo-empresa">
            </a>
<<<<<<< HEAD
            <div class="nav-links">
                <a href="/proyecto-final/gest-jugador/jugador.php" class="header-nav-link">Jugadores</a>
                <a href="/proyecto-final/gest-club/club.php" class="header-nav-link">Clubes</a>
                <a href="/proyecto-final/gest-fixture/fixtures.php" class="header-nav-link active">Fixture</a>
                <a href="#" class="header-nav-link">Sanciones</a>
=======
            <div class="nav-izquierda-botones-container">
                <a href="/proyecto-final/gest-jugador/jugador.php" class="nav-izquierda-botones">Jugadores</a>
                <a href="/proyecto-final/gest-club/club.php" class="nav-izquierda-botones">Clubes</a>
                <a href="fixtures.php" class="nav-izquierda-botones">Fixture</a>
                <!-- URL MANUAL PORQUE NO ANDA DE FORMA NINGUNA QUE NO SEA ESTA" -->
                <a href="http://localhost/proyecto-final/gest-sanciones/sanciones.php" class="nav-izquierda-botones">Sanciones</a>
>>>>>>> 8c668e4e13ca4e14feb238303e79f2be35433057
            </div>
        </nav>

        <div class="nav-derecha-container">
            <a href="#" class="nav-derecha-item">
                <i class="bi bi-bell"></i>
            </a>

            <a href="#" class="nav-derecha-item">
                <i class="bi bi-person-circle"></i>
                <span></span>
            </a>

            <a href="/proyecto-final/index.php" class="nav-derecha-item">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar sesión</span>
            </a>
        </div>
    </header>

    <div class="layout-abajo-header">
        <div class="dashboard-container">
            <h1> Bienvenido a la Página Principal de Admins!</h1>

            <!-- DE ACA EN DELANTE ES CARRUSEL. -->
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

                        // Genera una diapositiva y un enlace de eliminación por imagen.
                        foreach ($imagenes as $ruta) {
                            $nombreArchivo = basename($ruta);
                            echo '<div class="slide-item">';
                            echo '  <img src="' . htmlspecialchars($ruta) . '" alt="Imagen carrusel">';
                            echo '  <a href="eliminar_imagen.php?archivo=' . urlencode($nombreArchivo) . '"
                                       class="btn-eliminar-img"
                                       onclick="return confirm(\'¿Eliminar esta imagen?\');">🗑</a>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Permite seleccionar una o varias imágenes para el carrusel. -->
                <form action="subir_imagen.php" method="POST" enctype="multipart/form-data" id="formCarrusel">
                    <input type="file" name="nuevaImagen[]" id="carruselb" accept="image/*" multiple style="display:none;">
                    <button type="button" class="carruselb" onclick="document.getElementById('carruselb').click();">
                        Agregar / Cambiar imágenes
                    </button>
                </form>

                <!-- Envía el formulario automáticamente después de seleccionar archivos. -->
                <script>
                    document.getElementById('carruselb').addEventListener('change', function() {
                        if (this.files.length > 0) {
                            document.getElementById('formCarrusel').submit();
                        }
                    });
                </script>
            </div>
            <!-- TERMINA CARRUSEL -->
        </div>
    </div>
</body>
</html>
