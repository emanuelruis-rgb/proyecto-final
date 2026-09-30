<?php
session_name('club_session');
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto-final/conexion-bd/conexion.php";
$conexion = connection();

$nombreSesionClub = $_SESSION['nombre'] ?? '';
$idClub = $_SESSION['idClub'];
$nombreMostrarClub = $nombreSesionClub !== '' ? $nombreSesionClub : 'Club';

$stmt = mysqli_prepare($conexion, "SELECT nombreClub FROM club WHERE nombreClub = ?");
mysqli_stmt_bind_param($stmt, "s", $nombreSesionClub);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if ($fila = mysqli_fetch_assoc($resultado)) {
    $nombreMostrarClub = $fila['nombreClub'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jugadores</title>
    <link rel="stylesheet" href="jugadores-club-style.css">
    <link rel="icon" type="image/png" href="/img/logo-liga/log-liga-b.png">
</head>
<body>
    <header> 
        <nav class="nav-izquierda-container">
            <a href="/proyecto-final/club/indexclub.php">
                <img src="/proyecto-final/img/logo-liga/log-liga-d.png" alt="Logo" class="logo-empresa">
            </a>
            <div class="nav-izquierda-botones-container">
                <!-- aca van los jugadores propios y despues da la opcion de seleccionar los ajenos-->
                <a href="" class="nav-izquierda-botones">Jugadores</a>
            </div>
        </nav>

        <!-- Identifica el espacio de trabajo sin añadir otra opción al navbar. -->
        <div class="club-header-context" aria-label="Sección actual">
            <span>Jugadores del club</span>
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
    <main>
        <div class="boton-alternar-tabla-container">
            <a href="jugadores-club.php?tipo=propios" class="boton-alternar-tabla">Nuestros jugadores</a>
            <a href="jugadores-club.php?tipo=ajenos" class="boton-alternar-tabla">Jugadores ajenos</a>
        </div>

        <div class="tabla">
            <h2>Jugadores</h2>
            <table>
                <thead>
                    <tr>
                        <th>CI</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Club</th>
                        <th>Fecha de nacimiento</th>
                        <th>Género</th>
                        <th>Categoría</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- este while itera por las sanciones con el join de allá arriba
                    y $sancion es una sancion invidual
                    <?php // while ($sancion = mysqli_fetch_array($querySanciones)): ?>-->
                <tr>
                    <!-- cada uno de estos td define una columna, y busca una columna en la bd, en orden -->
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>