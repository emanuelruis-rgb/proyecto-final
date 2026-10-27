<?php
// Vista imprimible del fixture. Va en la misma carpeta que fixtures.php,
// porque usa sus mismas funciones (obtenerFixture y formatoHora).

include(__DIR__ . "/../../conexion-bd/conexion.php");
include(__DIR__ . "/fixtures-data.php");

$con = connection();

// Mismo id que usa jugador.php para la categoría femenina
define('ID_FEMENINO', 9);

// Texto de una categoría: "Femenino" o el año
function nombreCategoria($categoria) {
    return (int)$categoria['idCategoria'] === ID_FEMENINO ? 'Femenino' : $categoria['año'];
}

// Listas para los filtros
$competiciones = mysqli_fetch_all(
    mysqli_query($con, "SELECT idCompeticion, nombreCompeticion FROM competicion ORDER BY nombreCompeticion"),
    MYSQLI_ASSOC
);
$categorias = mysqli_fetch_all(
    mysqli_query($con, "SELECT idCategoria, `año` FROM categoria ORDER BY `año`"),
    MYSQLI_ASSOC
);

// Números de fecha que existen (para el selector)
$numerosFecha = obtenerNumerosFecha($con);

// Filtros elegidos (0 = todos)
$idCompeticion = (int)($_GET['competicion'] ?? 0);
$idCategoria   = (int)($_GET['categoria'] ?? 0);
$numeroFechaFiltro = (int)($_GET['fecha'] ?? 0);

// Partidos agrupados por fecha: [ '2026-12-02' => [partido, partido], ... ]
// Si se eligió una fecha, solo trae los partidos de esa fecha
$fixture = obtenerFixture($con, $idCompeticion, $idCategoria, $numeroFechaFiltro);

// Texto que se muestra debajo del título, según los filtros activos
$detalle = [];
foreach ($competiciones as $c) {
    if ((int)$c['idCompeticion'] === $idCompeticion) { $detalle[] = $c['nombreCompeticion']; }
}
foreach ($categorias as $c) {
    if ((int)$c['idCategoria'] === $idCategoria) { $detalle[] = 'Categoría ' . nombreCategoria($c); }
}
if ($numeroFechaFiltro) { $detalle[] = 'Fecha ' . $numeroFechaFiltro; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir fixture</title>
    <link rel="stylesheet" href="impresion.css">
</head>
<body>

    <!-- Filtros: al cambiar un select se recarga la página con el filtro aplicado -->
    <form method="GET" class="barra">
        <select name="competicion" onchange="this.form.submit()">
            <option value="0">Todas las competiciones</option>
            <?php foreach ($competiciones as $c): ?>
                <option value="<?= (int)$c['idCompeticion'] ?>" <?= (int)$c['idCompeticion'] === $idCompeticion ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['nombreCompeticion']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="categoria" onchange="this.form.submit()">
            <option value="0">Todas las categorías</option>
            <?php foreach ($categorias as $c): ?>
                <option value="<?= (int)$c['idCategoria'] ?>" <?= (int)$c['idCategoria'] === $idCategoria ? 'selected' : '' ?>>
                    <?= htmlspecialchars(nombreCategoria($c)) ?>
                </option>
            <?php endforeach; ?>
        </select>

                <select name="fecha" onchange="this.form.submit()">
            <option value="0">Todas las fechas</option>
            <?php foreach ($numerosFecha as $n): ?>
                <option value="<?= $n ?>" <?= $n === $numeroFechaFiltro ? 'selected' : '' ?>>Fecha <?= $n ?></option>
            <?php endforeach; ?>
        </select>

        <!-- window.print() abre la impresión del navegador (también permite guardar como PDF) -->
        <button type="button" onclick="window.print()">Imprimir fixture</button>
        <a href="fixtures.php" class="secundario">Volver</a>
    </form>

    <div class="documento">
        <div class="titulo">
            <div>
                <h1>Fixture de partidos</h1>
                <?php if ($detalle): ?><p><?= htmlspecialchars(implode(' · ', $detalle)) ?></p><?php endif; ?>
            </div>
        </div>

        <?php if (!$fixture): ?>
            <p class="vacio">No hay partidos para esta selección.</p>
        <?php endif; ?>

        <!-- Una tabla por cada fecha del campeonato -->
        <?php foreach ($fixture as $numeroFecha => $partidos): ?>
            <h2>Fecha <?= (int)$numeroFecha ?></h2>
            <table>
                <thead>
                    <tr><th>Día</th><th>Club local</th><th class="centro">Hora / resultado</th><th>Club visitante</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($partidos as $p): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($p['fechaPartido'])) ?></td>
                            <td><?= htmlspecialchars($p['local']) ?></td>
                            <td class="centro">
                                <?= htmlspecialchars(textoResultado($p)) ?>
                            </td>
                            <td><?= htmlspecialchars($p['visitante']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    </div>

</body>
</html>