<?php 
    /* ESTE ARCHIVO SIRVE COMO LA INTERFAZ PARA EDITAR LAS SANCIONES. NO PODES CAMBIAR EL ID, EL RESTO SI
    Y LA OPERACION EN LA BD DE REALMENTE CAMBIAR LA SANCION VA EN ejectura-editar-sancion.php */
    include(__DIR__ . "/../conexion-bd/conexion.php");
    $conexion = connection();

    /* recibe el id de clubes mandado por la ejecucion de este php en editar en la tabla de 
    sanciones */
    $idSancion = $_GET["idSancion"];

    /* recibe aca los datos mandados de sanciones.php para editarlos*/
    $query = mysqli_query(
        $conexion,
        "SELECT * FROM sancion WHERE idSancion = '$idSancion'"
        );

    $sancion = mysqli_fetch_array($query);

    /*query que busca los clubes para ponerlos en los option de select clubes */
    $queryClubes = mysqli_query($conexion, "SELECT * FROM club");
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
    <link rel="stylesheet" href="sanciones-style.css">
    <link rel="icon" type="image/png" href="../img/copa.png">
    <script src="sanciones.js" defer></script>
</head>
<body>
    <header> 
        <nav class="nav-izquierda-container">
            <a href="/proyecto-final/admin/indexadmin.php">
                <!-- Usa el mismo tamaño de logo que la pantalla principal de sanciones. -->
                <img src="/proyecto-final/img/logo-empresa/logo-empresa-blanco.png" alt="Logo" class="logo-liga">
            </a>
            <div class="nav-izquierda-botones-container">
                <a href="/proyecto-final/gest-jugador/jugador.php" class="nav-izquierda-botones">Jugadores</a>
                <a href="/proyecto-final/gest-club/club.php" class="nav-izquierda-botones">Clubes</a>
                <a href="/proyecto-final/admin/fixtures.php" class="nav-izquierda-botones">Fixture</a>
                <a href="/proyecto-final/gest-sanciones/sanciones.php" class="nav-izquierda-botones">Sanciones</a>
            </div>
        </nav>

        <div class="nav-derecha-container">
            <a href="#" class="nav-derecha-item">
                <i class="bi bi-bell"></i>
            </a>

            <a href="#" class="nav-derecha-item">
                <i class="bi bi-person-circle"></i>
                <span></span>
            </a>

            <a href="/proyecto-final/index.php" class="nav-derecha-item">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar sesión</span>
            </a>
        </div>
    </header>

    <div class="formulario-container">
        <h1>Editar sanción</h1>
        <form action="ejecutar-editar-sancion.php" method="POST">
            <input type="hidden" name="id-sancion" value="<?= $sancion['idSancion'] ?> (ID de la sanción)">

            <select name="id-club-sancion" id="select-club" class="select-formulario">
                <option value="" selected>Seleccionar club del sancionado</option>
                <!-- este php itera por los clubes y pone un option mas por cada club, con el value siendo el idclub y
                  $club es el array del club y el fetch array busca cada fila con la query de allá arriba-->
                <?php while ($club = mysqli_fetch_array($queryClubes)): ?>
                    <option value="<?= $club['idClub'] ?>">
                <?= $club['nombreClub'] ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <!-- se selecciona jugador de los disponibles en el club, y a la BD va su cedula-->
            <select name="ci-jugador-sancion" id="select-jugador" class="select-formulario">
                <option value="" disabled selected>Seleccionar jugador sancionado</option>

            </select>

            <select name="tipo-sancion" class="select-formulario">
                <option value="Amarilla">Tarjeta amarilla</option>
                <option value="Roja">Tarjeta roja</option>
                <option value="Disciplinaria">Sanción disciplinaria</option>
            </select>

            <input type="text" name="motivo-sancion" placeholder="Motivo de la sanción" required>
            <input type="number" name="numero-fechas" placeholder="Cantidad de fechas de suspensión" min="0" required>
            
            <input type="submit" value="Guardar cambios">
        </form>
    </div>
</body>
</html>