<?php
session_name('club_session');
session_start();
if (!isset($_SESSION['nombre'], $_SESSION['idClub'])) {
    header('Location: /proyecto-final/index.php');
    exit;
}

require_once __DIR__ . '/../../conexion-bd/conexion.php';
require_once __DIR__ . '/../../admin/fixture/fixtures-data.php';

$conexion = connection();
$resultadoCompeticiones = mysqli_query(
    $conexion,
    'SELECT idCompeticion, nombreCompeticion FROM competicion ORDER BY nombreCompeticion'
);
$competiciones = $resultadoCompeticiones
    ? mysqli_fetch_all($resultadoCompeticiones, MYSQLI_ASSOC)
    : [];
$resultadoCategorias = mysqli_query(
    $conexion,
    'SELECT idCategoria, `año` FROM categoria ORDER BY `año` DESC'
);
$categorias = $resultadoCategorias
    ? mysqli_fetch_all($resultadoCategorias, MYSQLI_ASSOC)
    : [];
$numerosFecha = obtenerNumerosFecha($conexion);

$idCompeticion = (int)($_GET['competicion'] ?? 0);
$idCategoria = (int)($_GET['categoria'] ?? 0);
$numeroFecha = (int)($_GET['fecha'] ?? 0);
$fixture = obtenerFixture($conexion, $idCompeticion, $idCategoria, $numeroFecha);

$detalle = [];
foreach ($competiciones as $competicion) {
    if ((int)$competicion['idCompeticion'] === $idCompeticion) {
        $detalle[] = $competicion['nombreCompeticion'];
    }
}
foreach ($categorias as $categoria) {
    if ((int)$categoria['idCategoria'] === $idCategoria) {
        $detalle[] = 'Categoría ' . $categoria['año'];
    }
}
if ($numeroFecha > 0) {
    $detalle[] = 'Fecha ' . $numeroFecha;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descargar fixture</title>
    <link rel="stylesheet" href="../../admin/fixture/impresion.css">
    <link rel="icon" type="image/png" href="../../img/logo-liga/log-liga-b.png">
</head>
<body>
    <form method="GET" class="barra">
        <select name="competicion" aria-label="Competición" onchange="this.form.submit()">
            <option value="0">Todas las competiciones</option>
            <?php foreach ($competiciones as $competicion): ?>
                <option value="<?= (int)$competicion['idCompeticion'] ?>"
                    <?= (int)$competicion['idCompeticion'] === $idCompeticion ? 'selected' : '' ?>>
                    <?= htmlspecialchars($competicion['nombreCompeticion'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="categoria" aria-label="Categoría" onchange="this.form.submit()">
            <option value="0">Todas las categorías</option>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?= (int)$categoria['idCategoria'] ?>"
                    <?= (int)$categoria['idCategoria'] === $idCategoria ? 'selected' : '' ?>>
                    <?= htmlspecialchars($categoria['año'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="fecha" aria-label="Fecha" onchange="this.form.submit()">
            <option value="0">Todas las fechas</option>
            <?php foreach ($numerosFecha as $fecha): ?>
                <option value="<?= $fecha ?>" <?= $fecha === $numeroFecha ? 'selected' : '' ?>>
                    Fecha <?= $fecha ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="button" onclick="window.print()">Descargar fixture</button>
        <a href="fixture.php" class="secundario">Volver</a>
    </form>

    <main class="documento">
        <header class="titulo">
            <h1>Fixture de partidos</h1>
            <?php if ($detalle): ?>
                <p><?= htmlspecialchars(implode(' · ', $detalle), ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </header>

        <?php if (!$fixture): ?>
            <p class="vacio">No hay partidos para esta selección.</p>
        <?php endif; ?>

        <?php foreach ($fixture as $fecha => $partidos): ?>
            <h2>Fecha <?= (int)$fecha ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>Día</th>
                        <th>Club local</th>
                        <th class="centro">Hora / resultado</th>
                        <th>Club visitante</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($partidos as $partido): ?>
                        <tr>
                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($partido['fechaPartido'])), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($partido['local'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="centro"><?= htmlspecialchars(textoResultado($partido), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($partido['visitante'], ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    </main>
</body>
</html>
