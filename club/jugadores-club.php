<?php
session_name('club_session');
session_start();
if (!isset($_SESSION['nombre'], $_SESSION['idClub'])) {
    header('Location: /proyecto-final/index.php');
    exit;
}

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
mysqli_stmt_close($stmt);

// Determina si la vista solicita jugadores del club autenticado o de los otros clubes.
$tipoJugadores = ($_GET['tipo'] ?? 'propios') === 'ajenos' ? 'ajenos' : 'propios';
$condicionClub = $tipoJugadores === 'propios' ? '= ?' : '<> ?';
$tituloTabla = $tipoJugadores === 'propios' ? 'Nuestros jugadores' : 'Jugadores ajenos';

// Carga la ficha de cada jugador y resuelve los nombres del club y de su categoría.
$sqlJugadores = "
    SELECT j.cedula, j.nombre, j.apellido, j.fechaNacimiento, j.genero,
           c.nombreClub, cat.`año` AS categoria
    FROM jugador j
    INNER JOIN club c ON j.idClub = c.idClub
    LEFT JOIN categoria cat ON j.idCategoria = cat.idCategoria
    WHERE j.idClub $condicionClub
    ORDER BY j.apellido ASC, j.nombre ASC";
$stmtJugadores = mysqli_prepare($conexion, $sqlJugadores);
$jugadores = [];
$errorJugadores = '';

if ($stmtJugadores === false) {
    $errorJugadores = 'No se pudo preparar la consulta de jugadores.';
} else {
    mysqli_stmt_bind_param($stmtJugadores, "i", $idClub);
    if (!mysqli_stmt_execute($stmtJugadores)) {
        $errorJugadores = 'No se pudo cargar la lista de jugadores.';
    } else {
        $resultadoJugadores = mysqli_stmt_get_result($stmtJugadores);
        while ($jugador = mysqli_fetch_assoc($resultadoJugadores)) {
            $jugadores[] = $jugador;
        }
        mysqli_free_result($resultadoJugadores);
    }
    mysqli_stmt_close($stmtJugadores);
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
                <a href="jugadores-club.php" class="nav-izquierda-botones active" aria-current="page">Jugadores</a>
                <a href="sanciones.php" class="nav-izquierda-botones">Sanciones</a>
                <a href="fixture.php" class="nav-izquierda-botones">Fixture</a>
                <a href="posiciones.php" class="nav-izquierda-botones">Tabla de posiciones</a>
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
            <a href="jugadores-club.php?tipo=propios" class="boton-alternar-tabla<?php echo $tipoJugadores === 'propios' ? ' active' : ''; ?>"<?php echo $tipoJugadores === 'propios' ? ' aria-current="page"' : ''; ?>>Nuestros jugadores</a>
            <a href="jugadores-club.php?tipo=ajenos" class="boton-alternar-tabla<?php echo $tipoJugadores === 'ajenos' ? ' active' : ''; ?>"<?php echo $tipoJugadores === 'ajenos' ? ' aria-current="page"' : ''; ?>>Jugadores ajenos</a>
        </div>

        <div class="tabla">
            <h2><?php echo htmlspecialchars($tituloTabla); ?></h2>
            <?php if ($errorJugadores !== ''): ?>
                <p role="alert"><?php echo htmlspecialchars($errorJugadores); ?></p>
            <?php endif; ?>
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
                    <?php if ($errorJugadores === '' && empty($jugadores)): ?>
                        <tr>
                            <td colspan="7">
                                <?php echo $tipoJugadores === 'propios'
                                    ? 'Este club todavía no tiene jugadores registrados.'
                                    : 'No hay jugadores registrados en otros clubes.'; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($jugadores as $jugador): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($jugador['cedula']); ?></td>
                                <td><?php echo htmlspecialchars($jugador['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($jugador['apellido']); ?></td>
                                <td><?php echo htmlspecialchars($jugador['nombreClub']); ?></td>
                                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($jugador['fechaNacimiento']))); ?></td>
                                <td><?php echo htmlspecialchars($jugador['genero']); ?></td>
                                <td><?php echo htmlspecialchars((string) ($jugador['categoria'] ?? 'Sin categoría')); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
