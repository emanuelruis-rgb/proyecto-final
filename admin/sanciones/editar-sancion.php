<?php 
    /* ESTE ARCHIVO SIRVE COMO LA INTERFAZ PARA EDITAR LAS SANCIONES. NO PODES CAMBIAR EL ID, EL RESTO SI
    Y LA OPERACION EN LA BD DE REALMENTE CAMBIAR LA SANCION VA EN ejectura-editar-sancion.php */
    include(__DIR__ . "/../../conexion-bd/conexion.php");
    $conexion = connection();

    /* recibe el id de clubes mandado por la ejecucion de este php en editar en la tabla de 
    sanciones. */
    $idSancion = $_GET["idSancion"];

    /* recibe aca los datos mandados de sanciones.php para editarlos*/
    $query = mysqli_query(
        $conexion,
        "SELECT * FROM sancion WHERE idSancion = '$idSancion'"
        );

    $sancion = mysqli_fetch_array($query);

    /*query que busca los clubes para ponerlos en los option de select clubes */
    $queryClubes = mysqli_query($conexion, "SELECT * FROM club");

    /* categorías para el selector (igual que en sanciones.php) */
    $categorias = mysqli_fetch_all(
        mysqli_query($conexion, "SELECT idCategoria, `año` FROM categoria ORDER BY `año` DESC"),
        MYSQLI_ASSOC
    );

    /* la categoría no está en la sanción sino en el jugador, así que se busca la del jugador sancionado
    para dejarla seleccionada */
    $cedulaActual = $sancion['cedulaJugador'];
    $queryJugador = mysqli_query($conexion, "SELECT idCategoria FROM jugador WHERE cedula = '$cedulaActual'");
    $jugadorActual = mysqli_fetch_assoc($queryJugador);
    $categoriaActual = $jugadorActual['idCategoria'] ?? '';

    $tipo   = $_POST['tipo-sancion'] ?? '';
$fechas = (int)($_POST['numero-fechas'] ?? 0);

if ($tipo === 'Amarilla') {
    $fechas = 0; // la amarilla nunca lleva fechas de suspensión
}
?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar sanciones</title>
    <link rel="stylesheet" href="/proyecto-final/admin/pagina-principal-admin.css">
    <link rel="stylesheet" href="sanciones-style.css">
    <link rel="icon" type="image/png" href="../../img/logo-liga/log-liga-b.png">
    <script src="sanciones.js" defer></script>
</head>
<body>
    <div class="formulario-container">
        <h1>Editar sanción</h1>
        <form action="ejecutar-editar-sancion.php" method="POST">
            <input type="hidden" name="id-sancion" value="<?= htmlspecialchars((string)$sancion['idSancion'], ENT_QUOTES, 'UTF-8') ?>">

            <select name="id-club-sancion" id="select-club" class="select-formulario">
                <option value="" disabled>Seleccionar club del sancionado</option>
                <!-- este php itera por los clubes y pone un option mas por cada club, con el value siendo el idclub y
                  $club es el array del club y el fetch array busca cada fila con la query de allá arriba-->
                <?php while ($club = mysqli_fetch_array($queryClubes)): ?>
                    <option value="<?= htmlspecialchars((string)$club['idClub'], ENT_QUOTES, 'UTF-8') ?>"
                        <?= (string)$club['idClub'] === (string)$sancion['clubSancion'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($club['nombreClub'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <!-- filtra los jugadores por categoría. No tiene name: no se guarda en la sanción,
            porque la categoría es del jugador -->
            <select id="select-categoria" class="select-formulario">
                <option value="">Todas las categorías</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= (int)$cat['idCategoria'] ?>"
                        <?= (string)$cat['idCategoria'] === (string)$categoriaActual ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['año']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- se selecciona jugador de los disponibles en el club, y a la BD va su cedula-->
            <select name="ci-jugador-sancion"
                id="select-jugador"
                class="select-formulario"
                required
                data-jugador-inicial="<?= htmlspecialchars((string)$sancion['cedulaJugador'], ENT_QUOTES, 'UTF-8') ?>">
                    <option value="" disabled selected>Seleccionar jugador sancionado</option>
            </select>

            <select name="tipo-sancion" class="select-formulario">
                <option value="Amarilla" <?= $sancion['tipo']==='Amarilla' ? 'selected':''?>>Tarjeta amarilla</option>
                <option value="Roja" <?= $sancion['tipo']==='Roja' ? 'selected':''?>>Tarjeta roja</option>
                <option value="Disciplinaria" <?= $sancion['tipo']==='Disciplinaria' ? 'selected':''?>>Sanción disciplinaria</option>
            </select>

            <input type="text" name="motivo-sancion"
                    value="<?= htmlspecialchars($sancion['motivo'],ENT_QUOTES, 'UTF-8') ?>" placeholder="Motivo de la sanción" required>
            <input type="number" name="numero-fechas" 
                    value="<?= htmlspecialchars((string)$sancion['fechaSuspencion'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Cantidad de fechas de suspensión" min="0" required>
            
            <input type="submit" value="Guardar cambios">
        </form>
    </div>
</body>
</html>