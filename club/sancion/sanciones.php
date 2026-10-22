<?php
session_name('club_session');
session_start();
if (!isset($_SESSION['nombre'], $_SESSION['idClub'])) {
    header('Location: /proyecto-final/index.php');
    exit;
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto-final/conexion-bd/conexion.php";
$conexion = connection();

/*query que busca los clubes para ponerlos en los option de select clubes */
    $queryClubes = mysqli_query($conexion, "SELECT * FROM club");
    /* join que junta las filas de acuerdo a la cédula del jugador y al club al que pertenece */
    $filtroClub      = (int)($_GET['filtro-club'] ?? 0);
$filtroCategoria = (int)($_GET['filtro-categoria'] ?? 0);

// Categorías para el selector del filtro
$categorias = mysqli_fetch_all(
    mysqli_query($conexion, "SELECT idCategoria, `año` FROM categoria ORDER BY `año` DESC"),
    MYSQLI_ASSOC
);

$where  = [];
$tipos  = "";
$params = [];

if ($filtroClub > 0) {
    $where[]  = "c.idClub = ?";
    $tipos   .= "i";
    $params[] = $filtroClub;
}
if ($filtroCategoria > 0) {
    $where[]  = "j.idCategoria = ?";
    $tipos   .= "i";
    $params[] = $filtroCategoria;
}

$sqlSanciones = "
    SELECT s.idSancion, j.cedula, j.nombre, j.apellido, cat.`año` AS categoria,
           c.nombreClub, s.tipo, s.motivo, s.fechaSuspencion
    FROM sancion s
    INNER JOIN jugador j   ON s.cedulaJugador = j.cedula
    INNER JOIN club c      ON j.idClub = c.idClub
    LEFT JOIN categoria cat ON j.idCategoria = cat.idCategoria"
    . ($where ? " WHERE " . implode(" AND ", $where) : "")
    . " ORDER BY s.idSancion DESC";

$stmtSanciones = mysqli_prepare($conexion, $sqlSanciones);
if ($params) {
    mysqli_stmt_bind_param($stmtSanciones, $tipos, ...$params);
}
mysqli_stmt_execute($stmtSanciones);
$querySanciones = mysqli_stmt_get_result($stmtSanciones);
$nombreSesionClub = $_SESSION['nombre'] ?? '';
$nombreMostrarClub = $nombreSesionClub !== '' ? $nombreSesionClub : 'Club';

$stmt = mysqli_prepare($conexion, "SELECT nombreClub FROM club WHERE nombreClub = ?");
mysqli_stmt_bind_param($stmt, "s", $nombreSesionClub);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if ($fila = mysqli_fetch_assoc($resultado)) {
    $nombreMostrarClub = $fila['nombreClub'];
}

require_once __DIR__ . '/../boletines-data.php';

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
    <title>Sanciones</title>
    <link rel="stylesheet" href="../pagina-principal-club.css?v=6">
    <link rel="stylesheet" href="/proyecto-final/include/css/style.css?v=1">
    <script src="menu-club.js" defer></script>
    <link rel="icon" type="image/png" href="../../img/logo-liga/log-liga-b.png">
</head>
<body>
    <header> 
        <nav class="nav-izquierda-container">
            <a href="/proyecto-final/club/indexclub.php">
                <img src="/proyecto-final/img/logo-liga/log-liga-d.png" alt="Logo" class="logo-empresa">
            </a>
            <div class="nav-izquierda-botones-container">
                <a href="../jugador/jugadores-club.php?tipoClub=propio" class="nav-izquierda-botones">Jugadores</a>
                <a href="sanciones.php" class="nav-izquierda-botones active" aria-current="page">Sanciones</a>
                <a href="../fixture/fixture.php" class="nav-izquierda-botones">Fixture</a>
                <a href="../tabla-posicion/posiciones.php" class="nav-izquierda-botones">Tabla de posiciones</a>
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
                <a href="/proyecto-final/uploads/documentos/reglamento.pdf" class="user-menu-item" download>
                    <i class="bi bi-file-earmark-text"></i> Descargar documento
            </a>
            <a href="/proyecto-final/logout.php" class="user-menu-item">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar sesión</span>
            </a>
            </div>
        </div>
    </header>
    <div class="tabla">
        <h2>Sanciones registradas</h2>
        <form class="filtros" method="GET" class="filtros-sanciones">
    <select name="filtro-club" onchange="this.form.submit()">
        <option value="0">Todos los clubes</option>
        <?php mysqli_data_seek($queryClubes, 0); // el while de arriba ya recorrió el resultado ?>
        <?php while ($club = mysqli_fetch_array($queryClubes)): ?>
            <option value="<?= $club['idClub'] ?>" <?= $club['idClub'] == $filtroClub ? 'selected' : '' ?>>
                <?= htmlspecialchars($club['nombreClub']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <select name="filtro-categoria" onchange="this.form.submit()">
    <option value="0">Todas las categorías</option>
    <?php foreach ($categorias as $cat): ?>
        <option value="<?= $cat['idCategoria'] ?>" <?= $cat['idCategoria'] == $filtroCategoria ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['año']) ?>
        </option>
    <?php endforeach; ?>
</select>

    <?php if ($filtroClub || $filtroCategoria): ?>
    <a href="sanciones.php"class="limpiar-filtros">Limpiar filtros</a>
<?php endif; ?>
        <table>
            <thead>
                <tr>
                    <!-- YA ESTAN CAMBIADOS LOS NOMBRES AHORA HAY QUE ADAPTAR EL FORMULARIO DE INTRODUCCION PARA Sanciones
                     EL SISTEMA DE RECUPERACION PARA PONER LAS SANCIONES EN LA TABLA INFERIOR LO HAGO DE CERO -->
                    <th>CI</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Club</th>
                    <th>Tipo de Sanción</th>
                    <th>Motivo</th>
                    <th>Fechas suspensión</th>
                    <!-- Celdas vacías para conservar el ancho de los botones. -->
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <!-- este while itera por las sanciones con el join de allá arriba
                 y $sancion es una sancion invidual -->
                <?php while ($sancion = mysqli_fetch_array($querySanciones)): ?>
            <tr>
                <!-- cada uno de estos td define una columna, y busca una columna en la bd, en orden -->
                <td><?= $sancion['cedula'] ?></td>
                <td><?= $sancion['nombre'] ?></td>
                <td><?= $sancion['apellido'] ?></td>
                <td><?= $sancion['nombreClub'] ?></td>
                <td><?= $sancion['tipo'] ?></td>
                <td><?= $sancion['motivo'] ?></td>
                <td><?= $sancion['fechaSuspencion'] ?></td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php include_once __DIR__ . '/../../include/footer.php'; ?>
</body>
</html>