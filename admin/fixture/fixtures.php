<?php
// La gestión del fixture está reservada a una sesión autenticada de administrador.
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
$idCompeticionFiltro = (int)($_GET['competicion'] ?? 0);
$idCategoriaFiltro = (int)($_GET['categoria'] ?? 0);
$categorias = mysqli_fetch_all(mysqli_query($con, "SELECT idCategoria, `año` FROM categoria ORDER BY `año` DESC"), MYSQLI_ASSOC);
$mensaje = '';
$idFechaFiltro = (int)($_GET['fecha'] ?? 0);   // filtro por número de fecha (0 = todas)
$numerosFecha = obtenerNumerosFecha($con);

function clubesHabilitados($con, $d) {
  $sql = "SELECT COUNT(DISTINCT cc.idClub)
          FROM club_categoria cc
          JOIN club_competicion cp ON cp.idClub = cc.idClub
          JOIN club c ON c.idClub = cc.idClub
          WHERE cc.idCategoria = ? AND cp.idCompeticion = ? AND c.activo = 1 AND cc.idClub IN (?, ?)";
  $stmt = mysqli_prepare($con, $sql);
  mysqli_stmt_bind_param($stmt, "iiii", $d['idCategoria'], $d['idCompeticion'], $d['idClubLocal'], $d['idClubVisitante']);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $cantidad);
  mysqli_stmt_fetch($stmt);
  mysqli_stmt_close($stmt);
  return (int)$cantidad === 2;
}

// El administrador puede crear partidos o cargar el resultado de uno pendiente.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['guardarPartido'])) {
    $guardado = guardarPartido($con, (int)($_POST['idPartido'] ?? 0), $_POST['estado'] ?? '',
      max(0, (int)($_POST['golesLocal'] ?? 0)), max(0, (int)($_POST['golesVisitante'] ?? 0)));

    // Si salió bien, vuelve a la misma página conservando los filtros
    if ($guardado) {
      header('Location: fixtures.php?' . http_build_query(['competicion' => (int)($_POST['competicion'] ?? 0),
        'categoria' => (int)($_POST['categoria'] ?? 0), 'fecha' => (int)($_POST['fecha'] ?? 0)]));
      exit;
    }
    $mensaje = 'No se pudo guardar el partido.';
  } else {
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
      'idCompeticion' => (int)($_POST['idCompeticion'] ?? 0),
      'idCategoria' => (int)($_POST['idCategoria'] ?? 0),
      'numeroFecha' => (int)($_POST['numeroFecha'] ?? 0),
      'jugado' => 0,
    ];

    // Evita guardar un partido entre el mismo club y valida los datos básicos.
    if ($datos['idClubLocal'] === $datos['idClubVisitante']) {
      $mensaje = 'El club local y visitante no pueden ser el mismo.';
     } elseif (!$datos['idClubLocal'] || !$datos['idClubVisitante'] || !$datos['fechaPartido'] || !$datos['idCompeticion'] || !$datos['idCategoria'] || $datos['numeroFecha'] < 1) {
      $mensaje = 'Completa los clubes, el número de fecha, el día, la competición y la categoría del partido.';
    } elseif (!clubesHabilitados($con, $datos)) {
      $mensaje = 'Ambos clubes deben estar activos y participar en la competición y la categoría elegidas.';
    } elseif (clubYaJuegaEnFecha($con, $datos)) {
      $mensaje = 'Alguno de los clubes ya tiene un partido en esa fecha del campeonato.';
    } elseif (crearPartido($con, $datos)) {
      header('Location: fixtures.php');
      exit;
    } else {
      $mensaje = 'Ocurrió un error al guardar el partido.';
    }
  }
}

$fixture = obtenerFixture($con, $idCompeticionFiltro, $idCategoriaFiltro, $idFechaFiltro);
$nombreMostrar = $_SESSION['nombre'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión del fixture</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../pagina-principal-admin.css?v=5">
  <link rel="stylesheet" href="fixtures-style.css?v=3">
  <link rel="stylesheet" href="/proyecto-final/include/css/style.css?v=1">
  <link rel="icon" type="image/png" href="../../img/logo-liga/log-liga-b.png">
  <script src="partido-panel.js?v=1" defer></script>
</head>
<body>
  <header> 
        <nav class="nav-izquierda-container">
            <a href="/proyecto-final/admin/indexadmin.php">
                <img src="/proyecto-final/img/logo-liga/log-liga-b.png" alt="Logo" class="logo-liga">
            </a>
            <div class="nav-izquierda-botones-container">
                <a href="../jugador/jugador.php" class="nav-izquierda-botones">Jugadores</a>
                <a href="../club/club.php" class="nav-izquierda-botones ">Clubes</a>
                <a href="fixtures.php" class="nav-izquierda-botones active">Fixture</a>                
                <a href="../sanciones/sanciones.php" class="nav-izquierda-botones">Sanciones</a>
                <a href="posiciones.php" class="nav-izquierda-botones">Tabla de posiciones</a>
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
            <a href="/proyecto-final/logout.php" class="user-menu-item">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar sesión</span>
            </a>
            </div>
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
      
      <label for="idCompeticion">Competicion</label>
      <select name="idCompeticion" id="idCompeticion" required>
        <option value="">Seleccionar</option>
        <?php foreach ($competiciones as $competicion): ?>
          <option value="<?= $competicion['idCompeticion'] ?>"><?= htmlspecialchars($competicion['nombreCompeticion']) ?></option>
        <?php endforeach; ?>
      </select>

      <label for="idCategoria">Categoría</label>
      <select name="idCategoria" id="idCategoria" required>
        <option value="">Seleccionar</option>
        <?php foreach ($categorias as $cat): ?>
          <option value="<?= (int)$cat['idCategoria'] ?>"><?= htmlspecialchars($cat['año']) ?></option>
        <?php endforeach; ?>
      </select>

      <label for="numeroFecha">Número de fecha del campeonato</label>
      <input type="number" name="numeroFecha" id="numeroFecha" min="1" placeholder="1" required>

      <label for="fechaPartido">Día del partido</label>
      <input type="date" name="fechaPartido" id="fechaPartido" min="<?= date('Y-m-d') ?>" required>

      <label for="horaPartido">Hora</label>
      <input type="number" name="horaPartido" id="horaPartido" min="0" max="2359" placeholder="1530" required>

      <label for="estadio">Estadio</label>
      <input type="text" name="estadio" id="estadio" placeholder="Estadio">

      <label for="arbitro">Árbitro</label>
      <input type="text" name="arbitro" id="arbitro" placeholder="Árbitro">

      <button type="submit">Guardar</button>
    </form>
  </section>
    <!-- Tabla general para consultar rápidamente todos los partidos del fixture. -->
    <section class="tabla-fixture">
    <h1>Tabla de partidos</h1>

    <form method="GET" class="filtros">
      <select name="competicion" onchange="this.form.submit()">
        <option value="0">Todas las competiciones</option>
        <?php foreach ($competiciones as $c): ?>
          <option value="<?= $c['idCompeticion'] ?>" <?= $c['idCompeticion'] == $idCompeticionFiltro ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['nombreCompeticion']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <select name="categoria" onchange="this.form.submit()">
        <option value="0">Todas las categorías</option>
        <?php foreach ($categorias as $cat): ?>
          <option value="<?= (int)$cat['idCategoria'] ?>" <?= $cat['idCategoria'] == $idCategoriaFiltro ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['año']) ?>
          </option>
        <?php endforeach; ?>
      </select>

            <select name="fecha" onchange="this.form.submit()">
        <option value="0">Todas las fechas</option>
        <?php foreach ($numerosFecha as $n): ?>
          <option value="<?= $n ?>" <?= $n === $idFechaFiltro ? 'selected' : '' ?>>Fecha <?= $n ?></option>
        <?php endforeach; ?>
      </select>

      <a href="fixtures.php" class="limpiar-filtros">Limpiar filtros</a>
      <a class="imprimir-fixture" href="imprimir.fixture.php?<?= htmlspecialchars(http_build_query([
        'competicion' => $idCompeticionFiltro,
        'categoria' => $idCategoriaFiltro,
        'fecha' => $idFechaFiltro,
      ]), ENT_QUOTES, 'UTF-8') ?>">Imprimir fixture</a>
    </form>

    <?php if (!$fixture): ?>
      <p class="sin-partidos">No hay partidos para esta selección.</p>
    <?php else: ?>
      <div class="tabla-scroll">
      <table>
        <thead>
          <tr>
            <th>Fecha / día</th>
            <th>Club local</th>
            <th>Club visitante</th>
            <th>Hora / resultado</th>
            <th>Acción / resultado</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($fixture as $numeroFecha => $partidos): ?>
            <?php foreach ($partidos as $p): ?>
              <tr>
                <td>Fecha <?= (int)$numeroFecha ?> · <?= date('d/m/Y', strtotime($p['fechaPartido'])) ?></td>
                <td><?= htmlspecialchars($p['local']) ?></td>
                <td><?= htmlspecialchars($p['visitante']) ?></td>
                <td>
                  <?= htmlspecialchars(textoResultado($p)) ?>
                </td>
                <td>
                  <!-- Menú desplegable del partido: estado y marcador -->
                  <details class="panel-partido">
                    <summary>Gestionar partido</summary>
                    <form method="POST" class="panel-form">
                      <input type="hidden" name="idPartido" value="<?= (int)$p['idPartido'] ?>">
                      <input type="hidden" name="competicion" value="<?= $idCompeticionFiltro ?>">
                      <input type="hidden" name="categoria" value="<?= $idCategoriaFiltro ?>">
                      <input type="hidden" name="fecha" value="<?= $idFechaFiltro ?>">
                      <label>Estado
                        <!-- Al cambiar el estado, se muestra u oculta el marcador (solo un partido "jugado" lo tiene) -->
                        <select name="estado" class="sel-estado">
                          <?php foreach (['pendiente' => 'Pendiente', 'jugado' => 'Jugado'] as $valor => $texto): ?>
                            <option value="<?= $valor ?>" <?= $p['estado'] === $valor ? 'selected' : '' ?>><?= $texto ?></option>
                          <?php endforeach; ?>
                        </select>
                      </label>
                      <div class="bloque-marcador" <?= $p['estado'] !== 'jugado' ? 'hidden' : '' ?>>
                        <label>Goles <?= htmlspecialchars($p['local']) ?>
                          <input type="number" name="golesLocal" min="0" max="30" value="<?= (int)$p['golesLocal'] ?>">
                        </label>
                        <label>Goles <?= htmlspecialchars($p['visitante']) ?>
                          <input type="number" name="golesVisitante" min="0" max="30" value="<?= (int)$p['golesVisitante'] ?>">
                        </label>
                      </div>
                      <button type="submit" name="guardarPartido" value="1">Guardar</button>
                    </form>
                  </details>
                  <!-- Elimina el partido seleccionado sin mostrar un formulario de alta. -->
                  <a href="eliminar-partido.php?id=<?= $p['idPartido'] ?>"
                     class="eliminar"
                     onclick="return confirm('¿Eliminar este partido?')"><i class="bi bi-trash3-fill"></i></a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endforeach; ?>
        </tbody>
      </table>
      </div>
    <?php endif; ?>
    </section>
  

  <!-- Una tarjeta por cada fecha del campeonato -->
  <?php foreach ($fixture as $numeroFecha => $partidos): ?>
    <h2 class="jornada">Fecha <?= (int)$numeroFecha ?></h2>
    <div class="partidos">
      <?php foreach ($partidos as $p): ?>
        <div class="partido">
          <div class="equipo local"><?= htmlspecialchars($p['local']) ?></div>
          <!-- Marcador si ya se jugó; si no, la hora (o "Pospuesto" / "Suspendido") -->
          <div class="centro">
            <div class="<?= $p['estado'] === 'jugado' ? 'resultado' : 'hora' ?>"><?= htmlspecialchars(textoResultado($p)) ?></div>
          </div>
          <div class="equipo visita"><?= htmlspecialchars($p['visitante']) ?></div>
          <a href="eliminar-partido.php?id=<?= (int)$p['idPartido'] ?>" class="eliminar"
             onclick="return confirm('¿Eliminar este partido?')"><i class="bi bi-trash3-fill"></i></a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>

</main>
    <?php include_once __DIR__ . '/../../include/footer.php'; ?>
</body>
</html>