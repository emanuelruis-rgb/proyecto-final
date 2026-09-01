<?php
include(__DIR__ . "/../conexion-bd/conexion.php");
include(__DIR__ . "/fixtures-data.php");

$con = connection();
$clubes = obtenerClubes($con);
$mensaje = "";

// Si llegó el formulario por POST, procesamos la creación.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'idClubLocal'     => (int)$_POST['idClubLocal'],
        'idClubVisitante' => (int)$_POST['idClubVisitante'],
        'fechaPartido'    => $_POST['fechaPartido'],
        'horaPartido'     => (int)$_POST['horaPartido'],
        'estadio'         => $_POST['estadio'] ?? '',
        'arbitro'         => $_POST['arbitro'] ?? '',
        'golesLocal'      => 0,
        'golesVisitante'  => 0,
        'duracionPartido' => 0,
    ];

    if ($datos['idClubLocal'] === $datos['idClubVisitante']) {
        $mensaje = "El club local y visitante no pueden ser el mismo.";
    } elseif (crearPartido($con, $datos)) {
        header("Location: fixtures.php");
        exit;
    } else {
        $mensaje = "Ocurrió un error al guardar el partido.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nuevo partido</title>
  <link rel="stylesheet" href="/proyecto-final/gest-fixture/fixtures-style.css">
</head>
<body>

<header>
  <h1>Nuevo partido</h1>
</header>

<main>
  <?php if ($mensaje): ?>
    <p class="mensaje-error"><?= htmlspecialchars($mensaje) ?></p>
  <?php endif; ?>

  <form method="POST">
    <label>Club local</label>
    <select name="idClubLocal" required>
      <option value="">-- Seleccionar --</option>
      <?php foreach ($clubes as $c): ?>
        <option value="<?= $c['idClub'] ?>"><?= htmlspecialchars($c['nombreClub']) ?></option>
      <?php endforeach; ?>
    </select>

    <label>Club visitante</label>
    <select name="idClubVisitante" required>
      <option value="">-- Seleccionar --</option>
      <?php foreach ($clubes as $c): ?>
        <option value="<?= $c['idClub'] ?>"><?= htmlspecialchars($c['nombreClub']) ?></option>
      <?php endforeach; ?>
    </select>

    <label>Fecha</label>
    <input type="date" name="fechaPartido" required>

    <label>Hora (formato 24h, ej: 1530 para 15:30)</label>
    <input type="number" name="horaPartido" min="0" max="2359" required>

    <label>Estadio</label>
    <input type="text" name="estadio" placeholder="Ej: Estadio Central">

    <label>Árbitro</label>
    <input type="text" name="arbitro" placeholder="Ej: Pérez">

    <button type="submit">Guardar partido</button>
  </form>

  <p><a href="fixtures.php">Volver al fixture</a></p>
</main>

</body>
</html>
