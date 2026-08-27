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
  <link rel="stylesheet" href="/proyecto-final/gest-fixture/fixtures-style.css">
</head>
<body>

<header>
  <img alt="logo-atrivia" src="../img/logo-atrivia.png" class="logo">
  <h1>Fixtures</h1>
</header>

<main>
  <p><a href="crear-partido.php" class="btn-agregar">+ Agregar partido</a></p>

  <?php if (!$fixture): ?>
    <p>No hay partidos cargados todavía.</p>
  <?php endif; ?>

  <?php foreach ($fixture as $jornada => $partidos): ?>
    <h2 class="jornada">Jornada <?= $jornada ?></h2>
    <div class="partidos">
      <?php foreach ($partidos as $p): ?>
        <div class="partido">
          <div class="equipo local"><?= htmlspecialchars($p['local']) ?></div>
          <div class="centro">
            <?php if ($p['golesLocal'] === null): ?>
              <div class="hora"><?= htmlspecialchars($p['horaPartido']) ?></div>
            <?php else: ?>
              <div class="resultado"><?= $p['golesLocal'] ?> - <?= $p['golesVisitante'] ?></div>
            <?php endif; ?>
          </div>
          <div class="equipo visita"><?= htmlspecialchars($p['visitante']) ?></div>
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
