<?php
    include(__DIR__ . "/../conexion-bd/conexion.php");
    $conexion = connection();
    $queryClubes = mysqli_query($conexion, "SELECT * FROM club");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sanciones</title>
    <link rel="stylesheet" href="sanciones-style.css">
    <link rel="icon" type="image/png" href="../img/copa.png">
    <script src="sanciones.js" defer></script>
</head>
<body>
    <header> 
        <nav class="nav-izquierda-container">
            <a href="/proyecto-final/admin/indexadmin.php">
                <img src="/proyecto-final/img/logo-empresa/logo-empresa-blanco.png" alt="Logo" class="logo-empresa">
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
        <h1>Crear sanción</h1>
        <!-- aca el admin pone los datos de la sancion.
         en base al club seleccionado, despliega los jugadores del club y ahi se selecciona,
         autocompleta la cédula en base al jugador. la sancion agregada va a agregar-sancion.php -->
        <form action="agregar-sancion.php" method="POST">
            <!-- se selecciona club, y de aca a la BD va el id club -->
            <select name="id-club-sancion" id="select-club" class="select-formulario">
                <option value="" disabled selected>Seleccionar club del sancionado</option>
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
                <option value="" disabled selected>Seleccionar jugador sancionado</option>ç

            </select>

            <select name="tipo-sancion" class="select-formulario">
                <option value="" disabled selected>Tipo de sanción</option>
                <option value="amarilla">Tarjeta amarilla</option>
                <option value="roja">Tarjeta roja</option>
                <option value="disciplinaria">Sanción disciplinaria</option>
            </select>

            <input type="text" name="motivo-sancion" placeholder="Motivo de la sanción" required>
            <input type="number" name="numero-fechas" placeholder="Cantidad de fechas de suspensión" min="0" required>
            
            <input type="submit" value="Agregar">
        </form>
    </div>
<br><br>


    <div class="tabla">
        <h2>Clubes registrados</h2>
        <table>
            <thead>
                <tr>
                    <!-- YA ESTAN CAMBIADOS LOS NOMBRES AHORA HAY QUE ADAPTAR EL FORMULARIO DE INTRODUCCION PARA Sanciones
                     EL SISTEMA DE RECUPERACION PARA PONER LAS SANCIONES EN LA TABLA INFERIOR LO HAGO DE CERO -->
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Cédula</th>
                    <th></th>
                    <th>Año de fundación</th>
                    <th>Estadio</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_array($query)): ?>
                    <tr>
                        <th><?= $row['idClub'] ?></th>
                        <th><?= $row['nombreClub'] ?></th>
                        <th><?= $row['contraseñaClub'] ?></th>
                        <th><?= $row['nombrePresidente'] ?></th>
                        <th><?= $row['añoCreacion'] ?></th>
                        <th><?= $row['estadio'] ?></th>
                        <th><a href="actualizar-club.php?id=<?= $row['idClub'] ?>" class="tabla--edit">Editar</a></th>
                        <th><a href="eliminar-club.php?id=<?= $row['idClub'] ?>" class="tabla--delete" >Eliminar</a></th>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>
</html>