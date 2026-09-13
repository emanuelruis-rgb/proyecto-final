<?php
include(__DIR__ . "/../conexion-bd/conexion.php");
include(__DIR__ . "/fixtures-data.php");

$con = connection();
$clubes = obtenerClubes($con);
$mensaje = '';

// Procesa el alta desde la misma pantalla para no depender de un botón separado.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $datos = [
    'idClubLocal' => (int)($_POST['idClubLocal'] ?? 0),
    'idClubVisitante' => (int)($_POST['idClubVisitante'] ?? 0),
    'fechaPartido' => $_POST['fechaPartido'] ?? '',
    'horaPartido' => (int)($_POST['horaPartido'] ?? 0),
    'estadio' => $_POST['estadio'] ?? '',
    'arbitro' => $_POST['arbitro'] ?? '',
    'golesLocal' => 0,
    'golesVisitante' => 0,
    'duracionPartido' => 0,
  ];

  // Evita guardar un partido entre el mismo club y valida los datos básicos.
  if ($datos['idClubLocal'] === $datos['idClubVisitante']) {
    $mensaje = 'El club local y visitante no pueden ser el mismo.';
  } elseif (!$datos['idClubLocal'] || !$datos['idClubVisitante'] || !$datos['fechaPartido']) {
    $mensaje = 'Completa los clubes y la fecha del partido.';
  } elseif (crearPartido($con, $datos)) {
    // Actualiza la vista para mostrar la fecha recién guardada.
    header('Location: fixtures.php');
    exit;
  } else {
    $mensaje = 'Ocurrió un error al guardar el partido.';
  }
}

$fixture = obtenerFixture($con);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Fixtures</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="/proyecto-final/gest-fixture/fixtures-style.css">
  <link rel="icon" type="image/png" href="../img/logo-liga/log-liga-b.png">
</head>
<body>
  <header class="main-header">
        <nav class="main-nav">
            <a href="/proyecto-final/admin/indexadmin.php" class="brand-link">
                <img src="/proyecto-final/img/logo-liga/log-liga-b.png" alt="Logo" class="logo-liga">
            </a>
            <div class="nav-links">
                <a href="/proyecto-final/gest-jugador/jugador.php" class="header-nav-link">Jugadores</a>
                <a href="/proyecto-final/gest-club/club.php" class="header-nav-link">Clubes</a>
                <a href="/proyecto-final/gest-fixture/fixtures.php" class="header-nav-link active">Fixture</a>
                <a href="/proyecto-final/gest-sanciones/sanciones.php" class="header-nav-link">Sanciones</a>
            </div>
        </nav>

        <div class="header-derecha">

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
  <!--
    La vista del fixture permite cargar una fecha y luego consultar los
    partidos en una tabla resumen y agrupados por fecha.
  -->
  <!-- Formulario vertical para agregar directamente una fecha y su partido. -->
  <section class="tabla-alta-fixture">
    <h1>Agregar fecha de partido</h1>
    <?php if ($mensaje): ?>
      <p class="mensaje-error"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>
    <form method="POST">
      <label for="idClubLocal">Club local</label>
      <select name="idClubLocal" id="idClubLocal" required>
        <option value="">Seleccionar</option>
        <?php foreach ($clubes as $club): ?>
          <option value="<?= $club['idClub'] ?>"><?= htmlspecialchars($club['nombreClub']) ?></option>
        <?php endforeach; ?>
      </select>

      <label for="idClubVisitante">Club visitante</label>
      <select name="idClubVisitante" id="idClubVisitante" required>
        <option value="">Seleccionar</option>
        <?php foreach ($clubes as $club): ?>
          <option value="<?= $club['idClub'] ?>"><?= htmlspecialchars($club['nombreClub']) ?></option>
        <?php endforeach; ?>
      </select>

      <label for="fechaPartido">Fecha</label>
      <input type="date" name="fechaPartido" id="fechaPartido" required>

      <label for="horaPartido">Hora</label>
      <input type="number" name="horaPartido" id="horaPartido" min="0" max="2359" placeholder="1530" required>

      <label for="estadio">Estadio</label>
      <input type="text" name="estadio" id="estadio" placeholder="Estadio">

      <label for="arbitro">Árbitro</label>
      <input type="text" name="arbitro" id="arbitro" placeholder="Árbitro">

      <button type="submit">Guardar</button>
    </form>
  </section>

  <?php
    // Si no hay partidos cargados
    if (!$fixture):
  ?>
    <p>No hay partidos cargados todavía.</p>
  <?php endif; ?>

  <?php if ($fixture): ?>
    <!-- Tabla general para consultar rápidamente todos los partidos del fixture. -->
    <section class="tabla-fixture">
      <h1>Tabla de partidos</h1>
      <table>
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Club local</th>
            <th>Club visitante</th>
            <th>Hora / resultado</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($fixture as $partidos): ?>
            <?php foreach ($partidos as $p): ?>
              <tr>
                <td><?= date('d/m/Y', strtotime($p['fechaPartido'])) ?></td>
                <td><?= htmlspecialchars($p['local']) ?></td>
                <td><?= htmlspecialchars($p['visitante']) ?></td>
                <td>
                  <?php if ($p['golesLocal'] === null || ((int)$p['golesLocal'] === 0 && (int)$p['golesVisitante'] === 0)): ?>
                    <?= htmlspecialchars((string)$p['horaPartido']) ?>
                  <?php else: ?>
                    <?= (int)$p['golesLocal'] ?> - <?= (int)$p['golesVisitante'] ?>
                  <?php endif; ?>
                </td>
                <td>
                  <!-- Elimina el partido seleccionado sin mostrar un formulario de alta. -->
                  <a href="eliminar-partido.php?id=<?= $p['idPartido'] ?>"
                     class="eliminar"
                     onclick="return confirm('¿Eliminar este partido?')">🗑</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>
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
