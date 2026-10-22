<?php
// Impide que una sesión de club o un visitante cree partidos.
session_name('admin_session');
session_start();
if (!isset($_SESSION['nombre'])) {
    header('Location: /proyecto-final/index.php');
    exit;
}

include(__DIR__ . "/../../conexion-bd/conexion.php");
include(__DIR__ . "/fixtures-data.php");

$con = connection();
$clubes = obtenerClubes($con);
$resultadoCompeticiones = mysqli_query($con, "SELECT idCompeticion, nombreCompeticion FROM competicion ORDER BY nombreCompeticion");
$competiciones = $resultadoCompeticiones ? mysqli_fetch_all($resultadoCompeticiones, MYSQLI_ASSOC) : [];
$resultadoCategorias = mysqli_query($con, "SELECT idCategoria, `año` FROM categoria ORDER BY `año` DESC");
$categorias = $resultadoCategorias ? mysqli_fetch_all($resultadoCategorias, MYSQLI_ASSOC) : [];
$mensaje = "";

// Si llegó el formulario por POST, procesamos la creación.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Normaliza los campos recibidos y define el marcador inicial del partido.
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
        'idCompeticion'   => (int)($_POST['idCompeticion'] ?? 0),
        'idCategoria'     => (int)($_POST['idCategoria'] ?? 0),
        'jugado'          => 0,
    ];

    if ($datos['idClubLocal'] === $datos['idClubVisitante']) {
        $mensaje = "El club local y visitante no pueden ser el mismo.";
    } elseif (!$datos['idCompeticion'] || !$datos['idCategoria']) {
        $mensaje = "Selecciona la competición y la categoría del partido.";
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
  <link rel="stylesheet" href="/proyecto-final/admin/fixture/fixtures-style.css">
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

    <label>Competición</label>
    <select name="idCompeticion" required>
      <option value="">-- Seleccionar --</option>
      <?php foreach ($competiciones as $competicion): ?>
        <option value="<?= (int)$competicion['idCompeticion'] ?>"><?= htmlspecialchars($competicion['nombreCompeticion']) ?></option>
      <?php endforeach; ?>
    </select>

    <label>Categoría</label>
    <select name="idCategoria" required>
      <option value="">-- Seleccionar --</option>
      <?php foreach ($categorias as $categoria): ?>
        <option value="<?= (int)$categoria['idCategoria'] ?>"><?= htmlspecialchars($categoria['año']) ?></option>
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
