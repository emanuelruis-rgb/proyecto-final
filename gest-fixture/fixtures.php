<?php
include(__DIR__ . "/../conexion-bd/conexion.php");
include(__DIR__ . "/fixtures-data.php");

$con = connection();
$fixture = obtenerFixture($con);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Fixtures</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="/proyecto-final/gest-fixture/fixtures-style.css">
</head>
<body>
  <header class="main-header">
        <nav class="main-nav">
            <a href="/proyecto-final/admin/indexadmin.php" class="brand-link">
                <img src="/proyecto-final/img/logo-empresa/logo-empresa-blanco.png" alt="Logo" class="logo-empresa">
            </a>
            <div class="nav-links">
                <a href="/proyecto-final/gest-jugador/jugador.php" class="header-nav-link">Jugadores</a>
                <a href="/proyecto-final/gest-club/club.php" class="header-nav-link">Clubes</a>
                <a href="/proyecto-final/gest-fixture/fixtures.php" class="header-nav-link active">Fixture</a>
                <a href="#" class="header-nav-link">Sanciones</a>
            </div>
        </nav>

        <div class="header-derecha">
            <a href="#" class="header-item">
                <i class="bi bi-bell"></i>
            </a>

            <a href="#" class="header-item">
                <i class="bi bi-person-circle"></i>
            </a>

            <a href="../index.php" class="header-item">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar sesión</span>
            </a>
        </div>
    </header>



<main>
  <?php
    // Botón para abrir la pantalla de crear partido
  ?>
  <p><a href="crear-partido.php" class="btn-agregar">+ Agregar partido</a></p>

  <?php
    // Si no hay partidos cargados
    if (!$fixture):
  ?>
    <p>No hay partidos cargados todavía.</p>
  <?php endif; ?>

  <?php
    // Recorre los partidos agrupados por fecha
    foreach ($fixture as $fecha => $partidos):
  ?>
    <?php
      // Muestra la fecha del partido
    ?>
    <h2 class="jornada">Fecha: <?= date('d/m/Y', strtotime($fecha)) ?></h2>
    <div class="partidos">
      <?php
        // Recorre cada partido de esa fecha
        foreach ($partidos as $p):
      ?>
        <div class="partido">
          <?php
            // Muestra el equipo local
          ?>
          <div class="equipo local"><?= htmlspecialchars($p['local']) ?></div>
          <div class="centro">
            <?php if ($p['golesLocal'] === null || ((int)$p['golesLocal'] === 0 && (int)$p['golesVisitante'] === 0)): ?>
              <?php
                // Muestra la hora del partido
              ?>
              <div class="hora"><?= htmlspecialchars((string)$p['horaPartido']) ?></div>
            <?php else: ?>
              <?php
                // Muestra el marcador final
              ?>
              <div class="resultado"><?= (int)$p['golesLocal'] ?> - <?= (int)$p['golesVisitante'] ?></div>
            <?php endif; ?>
          </div>
          <?php
            // Muestra el equipo visitante
          ?>
          <div class="equipo visita"><?= htmlspecialchars($p['visitante']) ?></div>
          <?php
            // Enlace para eliminar el partido
          ?>
          <a href="eliminar-partido.php?id=<?= $p['idPartido'] ?>"
             class="eliminar"
             onclick="return confirm('¿Eliminar este partido?')">🗑</a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
</main>

</body>
</html>
