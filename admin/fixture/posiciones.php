<?php
session_name('admin_session');
session_start();
if (!isset($_SESSION['nombre'])) { header('Location: /proyecto-final/index.php'); exit; }

include(__DIR__ . "/../../conexion-bd/conexion.php");
$con = connection();

// Competiciones para el selector
$competiciones = mysqli_fetch_all(mysqli_query($con, "SELECT * FROM competicion"), MYSQLI_ASSOC);
$categorias = mysqli_fetch_all(mysqli_query($con, "SELECT idCategoria, año FROM categoria ORDER BY año"), MYSQLI_ASSOC);

$idCompeticion = (int)($_GET['competicion'] ?? ($competiciones[0]['idCompeticion'] ?? 0));
$idCategoria   = (int)($_GET['categoria'] ?? ($categorias[0]['idCategoria'] ?? 0));

// Si no viene ninguna por la URL, usa la primera

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

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "iiiiii", $idCompeticion, $idCategoria, $idCompeticion, $idCategoria, $idCategoria, $idCompeticion);
mysqli_stmt_execute($stmt);
$posiciones = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

$nombreMostrar = $_SESSION['nombre'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tabla de posiciones</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../pagina-principal-admin.css?v=10">
  <link rel="stylesheet" href="fixtures-style.css?v=1">
  <link rel="stylesheet" href="/proyecto-final/include/css/style.css?v=1">
  <link rel="icon" type="image/png" href="../../img/logo-liga/log-liga-b.png">
</head>
<body class="pagina-posiciones">
  <header> 
        <nav class="nav-izquierda-container">
            <a href="/proyecto-final/admin/indexadmin.php">
                <img src="/proyecto-final/img/logo-liga/log-liga-b.png" alt="Logo" class="logo-liga">
            </a>
            <div class="nav-izquierda-botones-container">
                <a href="../jugador/jugador.php" class="nav-izquierda-botones">Jugadores</a>
                <a href="../club/club.php" class="nav-izquierda-botones ">Clubes</a>
                <a href="fixtures.php" class="nav-izquierda-botones">Fixture</a>                
                <a href="../sanciones/sanciones.php" class="nav-izquierda-botones">Sanciones</a>
                <a href="posiciones.php" class="nav-izquierda-botones active">Tabla de posiciones</a>
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
                <a href="/proyecto-final/gest-documentos/documento.php" class="user-menu-item">
                    <i class="bi bi-file-earmark-text"></i> Subir documento
            </a>
            <a href="/proyecto-final/logout.php?rol=admin" class="user-menu-item">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar sesión</span>
            </a>
            </div>
        </div>
</header>
  <!-- Copiá acá el <header> de fixtures.php para mantener el mismo menú -->

  <main>
    <section class="tabla-fixture">
      <h1>Tabla de posiciones</h1>

      <form method="GET" class="filtros">
  <select name="competicion" onchange="this.form.submit()">
    <?php foreach ($competiciones as $c): ?>
      <option value="<?= $c['idCompeticion'] ?>" <?= $c['idCompeticion'] == $idCompeticion ? 'selected' : '' ?>>
        <?= htmlspecialchars($c['nombreCompeticion']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <select name="categoria" onchange="this.form.submit()">
    <?php foreach ($categorias as $cat): ?>
      <option value="<?= (int)$cat['idCategoria'] ?>" <?= $cat['idCategoria'] == $idCategoria ? 'selected' : '' ?>>
        <?= htmlspecialchars($cat['año']) ?>
      </option>
    <?php endforeach; ?>
  </select>
</form>

      <?php if (!$posiciones): ?>
        <p>Todavía no hay partidos jugados en esta competición. Cargá los resultados desde <a href="/proyecto-final/admin/fixture/fixtures.php?competicion=<?= $idCompeticion ?>">Fixture</a> para calcular las posiciones.</p>
      <?php else: ?>
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
                <td><?= $fila['PJ'] ?></td>
                <td><?= $fila['PG'] ?></td>
                <td><?= $fila['PE'] ?></td>
                <td><?= $fila['PP'] ?></td>
                <td><?= $fila['GF'] ?></td>
                <td><?= $fila['GC'] ?></td>
                <td><?= $fila['DG'] ?></td>
                <td><strong><?= $fila['Pts'] ?></strong></td>
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