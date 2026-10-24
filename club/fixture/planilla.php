<?php
// Planilla de partido para imprimir: se elige club y categoría, y se genera
// el frente (jugadores) y el dorso (árbitro, cambios, tarjetas, firmas).

require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto-final/conexion-bd/conexion.php";
$con = connection();

// En la base, la categoría femenina es la que tiene este id (el mismo que usa jugador.php)
define('ID_FEMENINO', 9);

// Texto que se muestra de una categoría: "Femenino" o el año (2013, 2014...)
function nombreCategoria($categoria) {
    return (int)$categoria['idCategoria'] === ID_FEMENINO ? 'Femenino' : $categoria['anio'];
}

// Listas para los selectores: todos los clubes y todas las categorías
$clubes = mysqli_fetch_all(
    mysqli_query($con, "SELECT idClub, nombreClub FROM club ORDER BY nombreClub"),
    MYSQLI_ASSOC
);
$categorias = mysqli_fetch_all(
    mysqli_query($con, "SELECT idCategoria, `año` AS anio FROM categoria ORDER BY `año`"),
    MYSQLI_ASSOC
);

/* Club y categoría elegidos (vienen en la URL: ?club=2&categoria=3).
   Consulta preparada: el "?" se reemplaza de forma segura por el valor,
   así nadie puede meter código SQL. "i" significa que el dato es un entero. */
$idClub      = (int)($_GET['club'] ?? 0);
$idCategoria = (int)($_GET['categoria'] ?? 0);

$club = null;
$categoria = null;

if ($idClub && $idCategoria) {
    $stmt = mysqli_prepare($con, "SELECT nombreClub, logoClub FROM club WHERE idClub = ?");
    mysqli_stmt_bind_param($stmt, "i", $idClub);
    mysqli_stmt_execute($stmt);
    $club = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    $stmt = mysqli_prepare($con, "SELECT idCategoria, `año` AS anio FROM categoria WHERE idCategoria = ?");
    mysqli_stmt_bind_param($stmt, "i", $idCategoria);
    mysqli_stmt_execute($stmt);
    $categoria = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

/* Reglas de la planilla:
   - Categorías 2013 a 2016: 9 titulares y cambios libres.
   - Resto (2017 a 2020 y femenino): 11 titulares, 6 cambios y 2 más en el entretiempo. */
if ($club && $categoria) {
    $esMenor = (int)$categoria['idCategoria'] !== ID_FEMENINO
            && $categoria['anio'] >= 2013 && $categoria['anio'] <= 2016;

    $titulares    = $esMenor ? 9 : 11;
    $suplentes    = $esMenor ? 7 : 8;   // renglones de suplentes (se puede cambiar)
    $textoCambios = $esMenor ? 'Cambios libres' : '6 cambios + 2 más en el entretiempo';

}

// Dibuja $cantidad renglones numerados para anotar jugadores
function renglones($cantidad) {
    for ($i = 1; $i <= $cantidad; $i++) {
        echo "<tr><td class='numero'>$i</td><td></td><td></td><td></td></tr>";
    }
}

// Dibuja $cantidad renglones vacíos con $columnas celdas (para cambios y tarjetas)
function renglonesVacios($cantidad, $columnas) {
    for ($i = 0; $i < $cantidad; $i++) {
        echo '<tr>' . str_repeat('<td></td>', $columnas) . '</tr>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planilla de partido</title>
    <!-- CSS general del sitio (header, colores y variables) y luego el propio de la planilla -->
    <link rel="stylesheet" href="/proyecto-final/admin/pagina-principal-admin.css?v=6">
    <link rel="stylesheet" href="planilla.css">
</head>
<body>

    <!-- Navbar del club (no se imprime). AJUSTAR los enlaces a las páginas reales de tu club.
         Si tus páginas del club ya tienen un header en un archivo aparte, podés reemplazar
         todo este bloque por: <?php /* include __DIR__ . '/../header-club.php'; */ ?> -->
    <header>
        <nav class="nav-izquierda-container">
            <a href="/proyecto-final/club/indexclub.php">
                <img src="/proyecto-final/img/logo-liga/log-liga-b.png" alt="Logo" class="logo-liga">
            </a>
            <div class="nav-izquierda-botones-container">
                <a href="/proyecto-final/club/indexclub.php" class="nav-izquierda-botones">Inicio</a>
                <a href="planilla.php" class="nav-izquierda-botones active">Planilla de partido</a>
            </div>
        </nav>
    </header>

<?php if (!$club || !$categoria): ?>

    <!-- Paso 1: elegir club y categoría (tarjeta con el mismo estilo de los formularios del sitio) -->
    <div class="selector-planilla">
        <h1>Planilla de partido</h1>
        <form method="GET">
            <select name="club" required>
                <option value="">Seleccionar club</option>
                <?php foreach ($clubes as $c): ?>
                    <option value="<?= (int)$c['idClub'] ?>"><?= htmlspecialchars($c['nombreClub']) ?></option>
                <?php endforeach; ?>
            </select>

            <select name="categoria" required>
                <option value="">Seleccionar categoría</option>
                <?php foreach ($categorias as $c): ?>
                    <option value="<?= (int)$c['idCategoria'] ?>"><?= htmlspecialchars(nombreCategoria($c)) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Generar planilla</button>
        </form>
    </div>

<?php else: ?>

    <div class="barra">
        <!-- window.print() abre la impresión del navegador (también permite guardar como PDF) -->
        <button type="button" onclick="window.print()">Imprimir planilla</button>
        <a href="planilla.php" class="secundario">Elegir otro club o categoría</a>
    </div>

    <!-- ===== FRENTE ===== -->
    <section class="hoja">
        <div class="encabezado">
            <?php if (!empty($club['logoClub'])): ?>
                <img src="/proyecto-final/<?= htmlspecialchars($club['logoClub']) ?>" alt="Logo">
            <?php endif; ?>
            <div>
                <h1><?= htmlspecialchars($club['nombreClub']) ?></h1>
                <p>Categoría <?= htmlspecialchars(nombreCategoria($categoria)) ?></p>
            </div>
        </div>

        <div class="campos">
            <div class="campo">Fecha:</div>
            <div class="campo">Rival:</div>
            <div class="campo">Director técnico:</div>
            <div class="campo">Ayudante técnico:</div>
            <div class="campo">Kinesiólogo:</div>
            <div class="campo">Delegado:</div>
        </div>

        <h2>Titulares <small>(<?= $titulares ?> jugadores)</small></h2>
        <table>
            <tr><th class="numero">N°</th><th>Apellido y nombre</th><th>Cédula</th><th>Camiseta</th></tr>
            <?php renglones($titulares); ?>
        </table>

        <h2>Suplentes <small>(<?= $textoCambios ?>)</small></h2>
        <table>
            <tr><th class="numero">N°</th><th>Apellido y nombre</th><th>Cédula</th><th>Camiseta</th></tr>
            <?php renglones($suplentes); ?>
        </table>
    </section>

    <!-- ===== DORSO ===== -->
    <section class="hoja">
        <div class="encabezado">
            <div>
                <h1><?= htmlspecialchars($club['nombreClub']) ?></h1>
                <p>Categoría <?= htmlspecialchars(nombreCategoria($categoria)) ?> · Dorso</p>
            </div>
        </div>

        <h2>Observaciones del árbitro</h2>
        <div class="observaciones"></div>

        <h2>Cambios <small>(<?= $textoCambios ?>)</small></h2>
        <table>
            <tr><th>Minuto</th><th>Sale (N°)</th><th>Entra (N°)</th></tr>
            <?php renglonesVacios(8, 3); ?>
        </table>

        <h2>Tarjetas</h2>
        <table>
            <tr><th>Minuto</th><th>Jugador (N°)</th><th>Amarilla / Roja</th><th>Motivo</th></tr>
            <?php renglonesVacios(5, 4); ?>
        </table>

        <p class="resultado">
            Resultado final: <?= htmlspecialchars($club['nombreClub']) ?><span></span> Rival<span></span>
        </p>

        <div class="firmas">
            <div class="firma">Firma del delegado</div>
            <div class="firma">Firma del árbitro</div>
        </div>
    </section>


<?php endif; ?>

</body>
</html>