<?php
session_name('admin_session');
session_start();
if (!isset($_SESSION['nombre'])) { header('Location: /proyecto-final/index.php'); exit; }

include(__DIR__ . "/../conexion-bd/conexion.php");
$con = connection();

// Competiciones para el selector
$competiciones = mysqli_fetch_all(mysqli_query($con, "SELECT * FROM competicion"), MYSQLI_ASSOC);

// Si no viene ninguna por la URL, usa la primera
$idCompeticion = (int)($_GET['competicion'] ?? ($competiciones[0]['idCompeticion'] ?? 0));

$sql = "SELECT
    club.nombreClub,
    COUNT(*)                AS PJ,
    SUM(t.gf > t.gc)        AS PG,
    SUM(t.gf = t.gc)        AS PE,
    SUM(t.gf < t.gc)        AS PP,
    SUM(t.gf)               AS GF,
    SUM(t.gc)               AS GC,
    SUM(t.gf) - SUM(t.gc)   AS DG,
    SUM(CASE WHEN t.gf > t.gc THEN 3
             WHEN t.gf = t.gc THEN 1
             ELSE 0 END)    AS Pts
FROM (
    SELECT idClubLocal AS idClub, golesLocal AS gf, golesVisitante AS gc
    FROM partido
    WHERE idCompeticion = ? AND jugado = 1
    UNION ALL
    SELECT idClubVisitante, golesVisitante, golesLocal
    FROM partido
    WHERE idCompeticion = ? AND jugado = 1
) AS t
INNER JOIN club ON club.idClub = t.idClub
GROUP BY club.idClub, club.nombreClub
ORDER BY Pts DESC, DG DESC, GF DESC";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "ii", $idCompeticion, $idCompeticion);
mysqli_stmt_execute($stmt);
$posiciones = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Tabla de posiciones</title>
  <link rel="stylesheet" href="/proyecto-final/gest-fixture/fixtures-style.css">
  <link rel="icon" type="image/png" href="../img/logo-liga/log-liga-b.png">
</head>
<body>
  <!-- Copiá acá el <header> de fixtures.php para mantener el mismo menú -->

  <main>
    <section class="tabla-fixture">
      <h1>Tabla de posiciones</h1>

      <form method="GET">
        <select name="competicion" onchange="this.form.submit()">
          <?php foreach ($competiciones as $c): ?>
            <option value="<?= $c['idCompeticion'] ?>" <?= $c['idCompeticion'] == $idCompeticion ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['nombreCompeticion']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </form>

      <?php if (!$posiciones): ?>
        <p>Todavía no hay partidos jugados en esta competición. Cargá los resultados desde <a href="/proyecto-final/gest-fixture/fixtures.php?competicion=<?= $idCompeticion ?>">Fixture</a> para calcular las posiciones.</p>
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
</body>
</html>