<?php
// Recupera la sesión exclusiva del administrador.
session_name('admin_session');
session_start();
if (!isset($_SESSION['nombre'])) {
    header('Location: /proyecto-final/index.php');
    exit;
}

// Carga la conexión compartida con la base de datos.
require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto-final/conexion-bd/conexion.php";
$conexion = connection();

// Obtiene el usuario guardado en la sesión y define un nombre alternativo.
$nombreSesion = $_SESSION['nombre'] ?? '';
$nombreMostrar = $nombreSesion !== '' ? $nombreSesion : 'Administrador';
$tokenCsrfBoletines = $_SESSION['csrf_boletines'] ?? '';

// Crea un token impredecible para validar las acciones POST de boletines.
if ($tokenCsrfBoletines === '') {
    $tokenCsrfBoletines = bin2hex(random_bytes(32));
    $_SESSION['csrf_boletines'] = $tokenCsrfBoletines;
}

// Busca el nombre real del administrador usando una consulta preparada.
$stmt = mysqli_prepare($conexion, "SELECT nombreUsuario FROM administrador WHERE nombreUsuario = ?");
mysqli_stmt_bind_param($stmt, "s", $nombreSesion);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

// Reemplaza el nombre alternativo si el administrador existe en la base.
if ($fila = mysqli_fetch_assoc($resultado)) {
    $nombreMostrar = $fila['nombreUsuario'];
}
mysqli_stmt_close($stmt);

// Separa el aviso de eliminación del aviso de publicación para mostrar cada uno junto a su acción.
$mensajeFlashBoletin = $_SESSION['mensaje_flash_boletin'] ?? null;
unset($_SESSION['mensaje_flash_boletin']);
$mensajeBoletin = '';
$tipoMensajeBoletin = '';
$mensajeEliminacionBoletin = $mensajeFlashBoletin['mensaje'] ?? '';
$tipoMensajeEliminacionBoletin = $mensajeFlashBoletin['tipo'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_boletin'])) {
    // Exige que la petición provenga del formulario del panel y no de una web externa.
    $tokenEnviado = $_POST['csrf_token'] ?? '';
    if (!is_string($tokenEnviado) || !hash_equals($tokenCsrfBoletines, $tokenEnviado)) {
        $mensajeEliminacionBoletin = 'No se pudo validar la solicitud. Recarga la página e inténtalo de nuevo.';
        $tipoMensajeEliminacionBoletin = 'error';
    } else {
        // Acepta únicamente identificadores enteros positivos para el borrado.
        $idBoletin = filter_var($_POST['idBoletin'] ?? null, FILTER_VALIDATE_INT);
        if ($idBoletin === false || $idBoletin === null || $idBoletin <= 0) {
            $mensajeEliminacionBoletin = 'El boletín seleccionado no es válido.';
            $tipoMensajeEliminacionBoletin = 'error';
        } else {
            // El identificador se enlaza como parámetro, nunca se concatena en el SQL.
            $stmtEliminar = mysqli_prepare($conexion, "DELETE FROM boletin WHERE idBoletin = ?");
            mysqli_stmt_bind_param($stmtEliminar, "i", $idBoletin);
            mysqli_stmt_execute($stmtEliminar);
            $boletinEliminado = mysqli_stmt_affected_rows($stmtEliminar) === 1;
            mysqli_stmt_close($stmtEliminar);

            if ($boletinEliminado) {
                $mensajeEliminacionBoletin = 'Boletín eliminado correctamente.';
                $tipoMensajeEliminacionBoletin = 'exito';
            } else {
                $mensajeEliminacionBoletin = 'No se encontró el boletín seleccionado; puede que ya haya sido eliminado.';
                $tipoMensajeEliminacionBoletin = 'error';
            }
        }
    }

    // Evita repetir el borrado al recargar y permite mostrar el resultado tras redirigir.
    $_SESSION['mensaje_flash_boletin'] = [
        'mensaje' => $mensajeEliminacionBoletin,
        'tipo' => $tipoMensajeEliminacionBoletin,
    ];
    header('Location: indexadmin.php#gestionar-boletines');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_boletin'])) {
    // Valida el título y asocia el boletín con el administrador autenticado.
    $tituloBoletin = trim($_POST['titulo'] ?? '');
    $idAdmin = $_SESSION['idAdmin'] ?? 0;

    if ($idAdmin <= 0) {
        $mensajeBoletin = 'Tu sesión de administrador no es válida. Vuelve a iniciar sesión.';
        $tipoMensajeBoletin = 'error';
    } elseif ($tituloBoletin === '' || strlen($tituloBoletin) > 40) {
        $mensajeBoletin = 'El título debe tener entre 1 y 40 caracteres.';
        $tipoMensajeBoletin = 'error';
    } else {
        // Guarda el boletín con parámetros para mantener segura la consulta.
        $stmtAgregar = mysqli_prepare(
            $conexion,
            "INSERT INTO boletin (idAdmin, fechaSubida, titulo) VALUES (?, CURDATE(), ?)"
        );
        mysqli_stmt_bind_param($stmtAgregar, "is", $idAdmin, $tituloBoletin);
        if (mysqli_stmt_execute($stmtAgregar)) {
            $mensajeBoletin = 'Boletín publicado correctamente.';
            $tipoMensajeBoletin = 'exito';
        } else {
            $mensajeBoletin = 'No se pudo publicar el boletín.';
            $tipoMensajeBoletin = 'error';
        }
        mysqli_stmt_close($stmtAgregar);
    }
}

// Carga los boletines ordenados del más reciente al más antiguo para administrarlos.
$boletines = [];
$resultadoBoletines = mysqli_query(
    $conexion,
    "SELECT idBoletin, fechaSubida, titulo FROM boletin ORDER BY fechaSubida DESC, idBoletin DESC"
);
if ($resultadoBoletines === false) {
    $mensajeBoletin = 'No se pudo cargar la lista de boletines.';
    $tipoMensajeBoletin = 'error';
} else {
    while ($boletin = mysqli_fetch_assoc($resultadoBoletines)) {
        $boletines[] = $boletin;
    }
    mysqli_free_result($resultadoBoletines);
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
    <link rel="stylesheet" href="pagina-principal-admin.css?v=6">
    <link rel="icon" type="image/png" href="../img/logo-liga/log-liga-b.png">
</head>
<body>
    <header> 
        <nav class="nav-izquierda-container">
            <a href="/proyecto-final/admin/indexadmin.php">
                <img src="/proyecto-final/img/logo-liga/log-liga-d.png" alt="Logo" class="logo-liga">
            </a>
            <div class="nav-izquierda-botones-container">
                <a href="gest-jugador/jugador.php" class="nav-izquierda-botones">Jugadores</a>
                <a href="gest-club/club.php" class="nav-izquierda-botones">Clubes</a>
                <a href="gest-fixture/fixtures.php" class="nav-izquierda-botones">Fixture</a>
                <a href="gest-sanciones/sanciones.php" class="nav-izquierda-botones">Sanciones</a>
                <a href="gest-fixture/posiciones.php" class="nav-izquierda-botones">Tabla de posiciones</a>
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
                <a href="gest-documentos/documento.php" class="user-menu-item">
                    <i class="bi bi-file-earmark-text"></i> Subir documento
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
            <section class="admin-introduccion" aria-labelledby="titulo-admin">
                <div class="admin-introduccion-icono" aria-hidden="true">
                    <i class="bi bi-speedometer2"></i>
                </div>
                <div>
                    <p class="admin-introduccion-etiqueta">Panel de administración</p>
                    <h1 id="titulo-admin">Bienvenido a la página principal</h1>
                    <p class="admin-introduccion-descripcion">Gestiona la información de la liga y mantén actualizadas las comunicaciones de los clubes.</p>
                </div>
            </section>

            <!-- Formulario que permite publicar sin usar phpMyAdmin. -->
            <section class="publicar-boletin" id="publicar-boletin" aria-labelledby="titulo-publicar-boletin">
                <div>
                    <p class="publicar-boletin-etiqueta">Comunicaciones</p>
                    <h2 id="titulo-publicar-boletin">Publicar boletín</h2>
                    <p>El comunicado aparecerá en la sección de boletines de los clubes.</p>
                </div>
                <form method="POST" class="publicar-boletin-form">
                    <label for="titulo-boletin">Título del boletín</label>
                    <div class="publicar-boletin-campo">
                        <input type="text" id="titulo-boletin" name="titulo" maxlength="40" required placeholder="Ej. Cambio de horario de la jornada">
                        <button type="submit" name="agregar_boletin"><i class="bi bi-send" aria-hidden="true"></i> Publicar</button>
                    </div>
                </form>
                <?php if ($mensajeBoletin !== ''): ?>
                    <p class="mensaje-boletin <?php echo $tipoMensajeBoletin; ?>">
                        <?php echo htmlspecialchars($mensajeBoletin); ?>
                    </p>
                <?php endif; ?>
            </section>

            <!-- Lista los comunicados existentes y ofrece borrado individual desde el panel. -->
            <section class="gestionar-boletines" id="gestionar-boletines" aria-labelledby="titulo-gestionar-boletines">
                <div class="gestionar-boletines-encabezado">
                    <div>
                        <p class="publicar-boletin-etiqueta">Administración</p>
                        <h2 id="titulo-gestionar-boletines">Boletines publicados</h2>
                    </div>
                    <span class="boletines-cantidad"><?php echo count($boletines); ?></span>
                </div>

                <?php if ($mensajeEliminacionBoletin !== ''): ?>
                    <p class="mensaje-boletin <?php echo $tipoMensajeEliminacionBoletin; ?>" role="status">
                        <?php echo htmlspecialchars($mensajeEliminacionBoletin); ?>
                    </p>
                <?php endif; ?>

                <?php if (empty($boletines)): ?>
                    <p class="boletines-sin-registros">Todavía no hay boletines publicados para eliminar.</p>
                <?php else: ?>
                    <ul class="boletines-admin-lista">
                        <?php foreach ($boletines as $boletin): ?>
                            <li class="boletin-admin-item">
                                <div class="boletin-admin-detalle">
                                    <h3><?php echo htmlspecialchars($boletin['titulo']); ?></h3>
                                    <time datetime="<?php echo htmlspecialchars($boletin['fechaSubida']); ?>">
                                        Publicado el <?php echo htmlspecialchars(date('d/m/Y', strtotime($boletin['fechaSubida']))); ?>
                                    </time>
                                </div>
                                <form method="POST" class="boletin-admin-eliminar" onsubmit="return confirm('¿Eliminar este boletín? Esta acción no se puede deshacer.');">
                                    <input type="hidden" name="idBoletin" value="<?php echo (int) $boletin['idBoletin']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($tokenCsrfBoletines); ?>">
                                    <button type="submit" name="eliminar_boletin" value="1">
                                        <i class="bi bi-trash" aria-hidden="true"></i>
                                        Eliminar
                                    </button>
                                    <button type="submit" name="actualizar_boletin" value="1">
                                        <i class="bi bi-pencil" aria-hidden="true"></i>
                                        Actualizar
                                    </button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

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
