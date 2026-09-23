<?php
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
    <!-- Carga Bootstrap, iconos, metadatos y estilos del panel. -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página principal admins</title>
    <link rel="stylesheet" href="pagina-principal-admin.css">
    <link rel="icon" type="image/png" href="../img/logo-liga/log-liga-b.png">
</head>
<body>
    <header> 
        <nav class="nav-izquierda-container">
            <a href="/proyecto-final/admin/indexadmin.php">
                <img src="/proyecto-final/img/logo-liga/log-liga-d.png" alt="Logo" class="logo-liga">
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
                <a href="documento.php" class="user-menu-item">
                    <i class="bi bi-person-badge"></i> Mi perfil
            </a>
            <a href="/proyecto-final/index.php" class="user-menu-item">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar sesión</span>
            </a>
            </div>
        </div>

    </header>

    <div class="layout-abajo-header">
        <div class="dashboard-container">
            <h1> Bienvenido a la Página Principal de Admins!</h1>

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

                        // Genera una diapositiva por cada imagen disponible.
                        foreach ($imagenes as $ruta) {
                            $nombreArchivo = basename($ruta);
                            echo '<div class="slide-item" data-archivo="' . htmlspecialchars($nombreArchivo) . '">';
                            echo '  <img src="' . htmlspecialchars($ruta) . '" alt="Imagen carrusel">';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Permite cambiar la imagen visible sin movimiento automático. -->
                <div class="controles-carrusel">
                    <button type="button" id="anteriorCarrusel" class="btn-carrusel">Anterior</button>
                    <span id="indicadorCarrusel">Imagen 1</span>
                    <button type="button" id="siguienteCarrusel" class="btn-carrusel">Siguiente</button>
                </div>

                <!-- Acciones de administración ubicadas debajo del carrusel. -->
                <div class="acciones-carrusel">
                    <form action="subir_imagen.php" method="POST" enctype="multipart/form-data" id="formCarrusel">
                        <input type="file" name="nuevaImagen[]" id="carruselb" accept="image/*" multiple style="display:none;">
                        <button type="button" class="carruselb" onclick="document.getElementById('carruselb').click();">
                            Agregar imágenes
                        </button>
                    </form>

                    <form action="eliminar_imagen.php" method="GET" onsubmit="return confirm('¿Eliminar la imagen que se está mostrando?');">
                        <!-- El archivo se actualiza según la diapositiva visible. -->
                        <input type="hidden" name="archivo" id="imagenAEliminar">
                        <button type="submit" class="btn-eliminar-img" id="botonEliminarImagen">Eliminar imagen actual</button>
                    </form>
                </div>

                <!-- Envía el formulario automáticamente después de seleccionar archivos. -->
                <script>
                    document.getElementById('carruselb').addEventListener('change', function() {
                        if (this.files.length > 0) {
                            document.getElementById('formCarrusel').submit();
                        }
                    });

                    // Control manual del carrusel: solo se muestra una imagen a la vez.
                    const diapositivas = document.querySelectorAll('.slide-item');
                    const indicador = document.getElementById('indicadorCarrusel');
                    const imagenAEliminar = document.getElementById('imagenAEliminar');
                    const botonEliminarImagen = document.getElementById('botonEliminarImagen');
                    let diapositivaActual = 0;

                    function mostrarDiapositiva(indice) {
                        if (diapositivas.length === 0) return;
                        diapositivaActual = (indice + diapositivas.length) % diapositivas.length;
                        diapositivas.forEach((diapositiva, posicion) => {
                            diapositiva.classList.toggle('activa', posicion === diapositivaActual);
                        });
                        // El botón elimina exactamente la imagen que está visible.
                        imagenAEliminar.value = diapositivas[diapositivaActual].dataset.archivo;
                        indicador.textContent = 'Imagen ' + (diapositivaActual + 1) + ' de ' + diapositivas.length;
                    }

                    document.getElementById('anteriorCarrusel').addEventListener('click', function() {
                        mostrarDiapositiva(diapositivaActual - 1);
                    });

                    document.getElementById('siguienteCarrusel').addEventListener('click', function() {
                        mostrarDiapositiva(diapositivaActual + 1);
                    });

                    mostrarDiapositiva(0);
                    botonEliminarImagen.disabled = diapositivas.length === 0;
                </script>
            </div>
            <!-- TERMINA CARRUSEL -->
        </div>
    </div>
</body>
</html>
