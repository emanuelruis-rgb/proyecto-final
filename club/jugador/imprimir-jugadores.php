<?php
session_name('club_session');
session_start();
if (!isset($_SESSION['nombre'], $_SESSION['idClub'])) {
    header('Location: /proyecto-final/index.php');
    exit;
}

require_once __DIR__ . '/../../conexion-bd/conexion.php';
$conexion = connection();

$resultadoClubes = mysqli_query($conexion, 'SELECT idClub, nombreClub FROM club ORDER BY nombreClub');
$clubes = $resultadoClubes ? mysqli_fetch_all($resultadoClubes, MYSQLI_ASSOC) : [];
$resultadoCategorias = mysqli_query($conexion, 'SELECT idCategoria, `año` FROM categoria ORDER BY `año` DESC');
$categorias = $resultadoCategorias ? mysqli_fetch_all($resultadoCategorias, MYSQLI_ASSOC) : [];

$idClubFiltro = (int)($_GET['club'] ?? 0);
$idCategoriaFiltro = (int)($_GET['categoria'] ?? 0);

$stmt = mysqli_prepare(
    $conexion,
    "SELECT j.cedula, j.nombre, j.apellido, j.fechaNacimiento, j.genero,
            c.nombreClub, cat.`año` AS categoria
     FROM jugador j
     INNER JOIN club c ON c.idClub = j.idClub
     INNER JOIN categoria cat ON cat.idCategoria = j.idCategoria
     WHERE (? = 0 OR j.idClub = ?) AND (? = 0 OR j.idCategoria = ?)
     ORDER BY c.nombreClub, cat.`año`, j.apellido, j.nombre"
);
mysqli_stmt_bind_param($stmt, 'iiii', $idClubFiltro, $idClubFiltro, $idCategoriaFiltro, $idCategoriaFiltro);
mysqli_stmt_execute($stmt);
$resultadoJugadores = mysqli_stmt_get_result($stmt);
$jugadores = $resultadoJugadores ? mysqli_fetch_all($resultadoJugadores, MYSQLI_ASSOC) : [];
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de jugadores</title>
    <link rel="stylesheet" href="imprimir-jugadores.css">
    <link rel="icon" type="image/png" href="../../img/logo-liga/log-liga-b.png">
</head>
<body>
    <form method="GET" class="barra">
        <label for="club">Club</label>
        <select id="club" name="club" onchange="this.form.submit()">
            <option value="0">Todos los clubes</option>
            <?php foreach ($clubes as $club): ?>
                <option value="<?= (int)$club['idClub'] ?>"
                    <?= (int)$club['idClub'] === $idClubFiltro ? 'selected' : '' ?>>
                    <?= htmlspecialchars($club['nombreClub'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="categoria">Categoría</label>
        <select id="categoria" name="categoria" onchange="this.form.submit()">
            <option value="0">Todas las categorías</option>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?= (int)$categoria['idCategoria'] ?>"
                    <?= (int)$categoria['idCategoria'] === $idCategoriaFiltro ? 'selected' : '' ?>>
                    <?= htmlspecialchars($categoria['año'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="button" onclick="window.print()">Imprimir lista</button>
        <a class="secundario" href="jugadores-club.php?tipoClub=propio">Volver a jugadores</a>
    </form>

    <main class="documento">
        <header class="titulo">
            <h1>Lista de jugadores</h1>
            <p>
                <?= $idClubFiltro === 0 ? 'Todos los clubes' : '' ?>
                <?php foreach ($clubes as $club): ?>
                    <?php if ((int)$club['idClub'] === $idClubFiltro): ?>
                        <?= htmlspecialchars($club['nombreClub'], ENT_QUOTES, 'UTF-8') ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php foreach ($categorias as $categoria): ?>
                    <?php if ((int)$categoria['idCategoria'] === $idCategoriaFiltro): ?>
                        <?= $idClubFiltro === 0 ? ' · ' : ' · ' ?>Categoría
                        <?= htmlspecialchars($categoria['año'], ENT_QUOTES, 'UTF-8') ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </p>
        </header>

        <?php if (!$jugadores): ?>
            <p class="vacio">No hay jugadores para esta selección.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Jugador</th>
                        <th>Club</th>
                        <th>Categoría</th>
                        <th>CI</th>
                        <th>Fecha de nacimiento</th>
                        <th>Género</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jugadores as $indice => $jugador): ?>
                        <tr>
                            <td><?= $indice + 1 ?></td>
                            <td><?= htmlspecialchars($jugador['apellido'] . ', ' . $jugador['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($jugador['nombreClub'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($jugador['categoria'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($jugador['cedula'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($jugador['fechaNacimiento'])), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($jugador['genero'], ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>
</html>
