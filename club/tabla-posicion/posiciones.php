<?php
// Protege la consulta de posiciones con la sesión propia de los clubes.
session_name('club_session');
session_start();
if (!isset($_SESSION['nombre'], $_SESSION['idClub'])) {
    header('Location: /proyecto-final/index.php');
    exit;
}

require_once __DIR__ . '/../../conexion-bd/conexion.php';
$conexion = connection();

// Competiciones y categorías para los selectores
$competiciones = mysqli_fetch_all(mysqli_query($conexion, "SELECT * FROM competicion"), MYSQLI_ASSOC);
$categorias = mysqli_fetch_all(mysqli_query($conexion, "SELECT idCategoria, año FROM categoria ORDER BY año"), MYSQLI_ASSOC);

// Si no viene ninguna por la URL, usa la primera
$idCompeticion = (int)($_GET['competicion'] ?? ($competiciones[0]['idCompeticion'] ?? 0));
$idCategoria   = (int)($_GET['categoria'] ?? ($categorias[0]['idCategoria'] ?? 0));

// Calcula estadísticas por club a partir de los partidos jugados de la competición y categoría seleccionadas.
$sql = "SELECT
    club.nombreClub,
    COUNT(t.idClub)                         AS PJ,
    COALESCE(SUM(t.gf > t.gc), 0)           AS PG,
    COALESCE(SUM(t.gf = t.gc), 0)           AS PE,
    COALESCE(SUM(t.gf < t.gc), 0)           AS PP,
    COALESCE(SUM(t.gf), 0)                  AS GF,
    COALESCE(SUM(t.gc), 0)                  AS GC,
    COALESCE(SUM(t.gf) - SUM(t.gc), 0)      AS DG,
    COALESCE(SUM(CASE WHEN t.gf > t.gc THEN 3
                      WHEN t.gf = t.gc THEN 1
                      ELSE 0 END), 0)       AS Pts
FROM club
LEFT JOIN (
    SELECT idClubLocal AS idClub, golesLocal AS gf, golesVisitante AS gc
    FROM partido
    WHERE idCompeticion = ? AND idCategoria = ? AND jugado = 1
    UNION ALL
    SELECT idClubVisitante, golesVisitante, golesLocal
    FROM partido
    WHERE idCompeticion = ? AND idCategoria = ? AND jugado = 1
) AS t ON t.idClub = club.idClub
WHERE club.activo = 1
  AND club.idClub IN (SELECT idClub FROM club_categoria WHERE idCategoria = ?)
  AND club.idClub IN (SELECT idClub FROM club_competicion WHERE idCompeticion = ?)
GROUP BY club.idClub, club.nombreClub
ORDER BY Pts DESC, DG DESC, GF DESC, club.nombreClub ASC";

$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, 'iiiiii', $idCompeticion, $idCategoria, $idCompeticion, $idCategoria, $idCategoria, $idCompeticion);
mysqli_stmt_execute($stmt);
$posiciones = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de posiciones</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../pagina-principal-club.css?v=7">
    <link rel="stylesheet" href="../../admin/fixture/fixtures-style.css?v=1">
    <link rel="stylesheet" href="/proyecto-final/include/css/style.css?v=1">
    <script src="../menu-club.js" defer></script>
    <link rel="icon" type="image/png" href="../../img/logo-liga/log-liga-b.png">
</head>
<body>
    <header>
        <!-- Mantiene al usuario dentro de las secciones de consulta del portal del club. -->
        <nav class="nav-izquierda-container">
            <a href="../indexclub.php">
                <img src="../../img/logo-liga/log-liga-d.png" alt="Logo" class="logo-empresa">
            </a>
            <div class="nav-izquierda-botones-container">
                <a href="../jugador/jugadores-club.php?tipoClub=propio" class="nav-izquierda-botones">Jugadores</a>
                <a href="../sancion/sanciones.php" class="nav-izquierda-botones">Sanciones</a>
                <a href="../fixture/fixture.php" class="nav-izquierda-botones">Fixture</a>
                <a href="posiciones.php" class="nav-izquierda-botones active" aria-current="page">Tabla de posiciones</a>
            </div>
        </nav>
        <div class="club-header-context" aria-label="Sección actual">
            <span>Portal del club</span>
            <small>Tabla de posiciones</small>
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
            <h1>Tabla de posiciones</h1>

            <form method="GET" class="filtros">
                <select name="competicion" onchange="this.form.submit()">
                    <?php foreach ($competiciones as $c): ?>
                        <option value="<?= (int)$c['idCompeticion'] ?>" <?= (int)$c['idCompeticion'] === $idCompeticion ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nombreCompeticion']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="categoria" onchange="this.form.submit()">
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= (int)$cat['idCategoria'] ?>" <?= (int)$cat['idCategoria'] === $idCategoria ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['año']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <?php if (!$posiciones): ?>
                <p>Todavía no hay partidos jugados en esta competición.</p>
            <?php else: ?>
                <!-- La tabla presenta estadísticas derivadas de los resultados cargados por administración. -->
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Club</th>
                            <th>PJ</th>
                            <th>PG</th>
                            <th>PE</th>
                            <th>PP</th>
                            <th>GF</th>
                            <th>GC</th>
                            <th>DG</th>
                            <th>Pts</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posiciones as $i => $fila): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($fila['nombreClub']) ?></td>
                                <td><?= (int)$fila['PJ'] ?></td>
                                <td><?= (int)$fila['PG'] ?></td>
                                <td><?= (int)$fila['PE'] ?></td>
                                <td><?= (int)$fila['PP'] ?></td>
                                <td><?= (int)$fila['GF'] ?></td>
                                <td><?= (int)$fila['GC'] ?></td>
                                <td><?= (int)$fila['DG'] ?></td>
                                <td><strong><?= (int)$fila['Pts'] ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
    <?php include_once __DIR__ . '/../../include/footer.php'; ?>
</body>
</html>