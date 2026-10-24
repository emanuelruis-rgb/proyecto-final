<?php
// Este endpoint pertenece al portal del club y solo permite consultas.
session_name('club_session');
session_start();
if (!isset($_SESSION['nombre'], $_SESSION['idClub'])) {
    header('Location: /proyecto-final/index.php');
    exit;
}

require_once __DIR__ . '/../../conexion-bd/conexion.php';
require_once __DIR__ . '/../../admin/fixture/fixtures-data.php';

$conexion = connection();
// El filtro es opcional: cero muestra todas las competiciones.
$resultadoCompeticiones = mysqli_query(
    $conexion,
    'SELECT idCompeticion, nombreCompeticion FROM competicion ORDER BY nombreCompeticion'
);
$competiciones = $resultadoCompeticiones
    ? mysqli_fetch_all($resultadoCompeticiones, MYSQLI_ASSOC)
    : [];
$idCompeticion = (int)($_GET['competicion'] ?? 0);
// Reutiliza la consulta común del fixture, sin cargar acciones administrativas.
// Categorías para el filtro (el cero muestra todas)
$categorias = mysqli_fetch_all(
    mysqli_query($conexion, 'SELECT idCategoria, `año` FROM categoria ORDER BY `año` DESC'),
    MYSQLI_ASSOC
);
$idCategoria = (int)($_GET['categoria'] ?? 0);

// Reutiliza la consulta común del fixture, ahora filtrando también por categoría.
$fixture = obtenerFixture($conexion, $idCompeticion, $idCategoria);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fixture de la liga</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../pagina-principal-club.css?v=6">
    <link rel="stylesheet" href="../../admin/fixture/fixtures-style.css?v=1">
    <link rel="stylesheet" href="/proyecto-final/include/css/style.css?v=1">
    <script src="../menu-club.js" defer></script>
    <link rel="icon" type="image/png" href="../../img/logo-liga/log-liga-b.png">
</head>
<body>
    <header>
        <!-- Navegación propia del club; no enlaza a las pantallas de gestión del admin. -->
        <nav class="nav-izquierda-container">
            <a href="../indexclub.php">
                <img src="../../img/logo-liga/log-liga-d.png" alt="Logo" class="logo-empresa">
            </a>
            <div class="nav-izquierda-botones-container">
                <a href="../jugador/jugadores-club.php?tipoClub=propio" class="nav-izquierda-botones">Jugadores</a>
                <a href="../sancion/sanciones.php" class="nav-izquierda-botones">Sanciones</a>
                <a href="fixture.php" class="nav-izquierda-botones active" aria-current="page">Fixture</a>
                <a href="../tabla-posicion/posiciones.php" class="nav-izquierda-botones">Tabla de posiciones</a>
                <a href="../tabla-posicion/posiciones.php" class="nav-izquierda-botones">Descargar fixture</a>
            </div>
        </nav>
        <div class="club-header-context" aria-label="Sección actual">
            <span>Portal del club</span>
            <small>Fixture de la liga</small>
        </div>
        <div class="user-menu">
            <button class="user-menu-toggle" onclick="toggleUserMenu()">
                <i class="bi bi-person-circle"></i>
                <span class="user-menu-name"><?= htmlspecialchars($_SESSION['nombre']) ?></span>
            </button>
            <div class="user-menu-dropdown" id="userDropdown">
                <a href="/proyecto-final/uploads/documentos/reglamento.pdf" class="user-menu-item" download>
                    <i class="bi bi-file-earmark-text"></i> Descargar documento
                </a>
                <a href="/proyecto-final/logout.php?rol=club" class="user-menu-item">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </div>
    </header>

    <main>
        <section class="tabla-fixture">
            <h1>Fixture de la liga</h1>
            <form method="GET">
                <label for="competicion">Competición</label>
                <select id="competicion" name="competicion" onchange="this.form.submit()">
                    <option value="0">Todas las competiciones</option>
                    <?php foreach ($competiciones as $competicion): ?>
                        <option value="<?= (int)$competicion['idCompeticion'] ?>"
                            <?= (int)$competicion['idCompeticion'] === $idCompeticion ? 'selected' : '' ?>>
                            <?= htmlspecialchars($competicion['nombreCompeticion']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="categoria">Categoría</label>
<select id="categoria" name="categoria" onchange="this.form.submit()">
    <option value="0">Todas las categorías</option>
    <?php foreach ($categorias as $cat): ?>
        <option value="<?= (int)$cat['idCategoria'] ?>"
            <?= (int)$cat['idCategoria'] === $idCategoria ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['año']) ?>
        </option>
    <?php endforeach; ?>
</select>
            </form>

            <?php if (!$fixture): ?>
                <p>No hay partidos para esta selección.</p>
            <?php else: ?>
                <!-- Se muestran horarios pendientes o marcadores finales, nunca controles de edición. -->
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Club local</th>
                            <th>Club visitante</th>
                            <th>Hora / resultado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fixture as $partidos): ?>
                            <?php foreach ($partidos as $partido): ?>
                                <tr>
                                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($partido['fechaPartido']))) ?></td>
                                    <td><?= htmlspecialchars($partido['local']) ?></td>
                                    <td><?= htmlspecialchars($partido['visitante']) ?></td>
                                    <td>
                                        <?php if ($partido['jugado']): ?>
                                            <?= (int)$partido['golesLocal'] ?> - <?= (int)$partido['golesVisitante'] ?>
                                        <?php else: ?>
                                            <?= htmlspecialchars(formatoHora($partido['horaPartido'])) ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
    <?php include_once __DIR__ . '/../../include/footer.php'; ?>
</body>
</html>