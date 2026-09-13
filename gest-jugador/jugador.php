<?php
include(__DIR__ . "/../conexion-bd/conexion.php");
$con = connection();

/* esta query hace join de jugador y club para poder no solo tener todos los datos del jugador
sino que también los del club. asi ponemos, en vez de idclub, ponemos nombreClub */
/*
    Cálculo de la fuerza peso:
    P = m x g, donde m es la masa en kg y g = 9,81 m/s².
    El resultado se expresa en Newtons (N), por eso se muestra como fuerzaPeso.
*/
$query = mysqli_query($con, "SELECT
    jugador.idClub,
    club.nombreClub,
    jugador.cedula,
    jugador.nombre,
    jugador.apellido,
    jugador.fechaNacimiento,
    jugador.genero,
    jugador.idCategoria,
    -- La fuerza peso se calcula con la gravedad terrestre aproximada de 9,81 m/s².
    jugador.masa,
    jugador.altura,
    jugador.velocidad,
    jugador.masa * 9.81 AS fuerzaPeso
FROM jugador
INNER JOIN club
    ON jugador.idClub = club.idClub;");
$queryCategoria = mysqli_query($con, "SELECT * FROM categoria");
$queryClub = mysqli_query($con, "SELECT * FROM club");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de jugadores</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/proyecto-final/gest-jugador/jug-style.css">
    <link rel="icon" type="image/png" href="../img/logo-liga/log-liga-b.png">
</head>
<body>
    
    <header> 
        <nav class="main-nav">
            <a href="/proyecto-final/admin/indexadmin.php">
                <img src="/proyecto-final/img/logo-liga/log-liga-b.png" alt="Logo" class="logo-liga">
            </a>
            <div class="nav-links">
                <a href="/proyecto-final/gest-jugador/jugador.php" class="header-nav-link active">Jugadores</a>
                <a href="/proyecto-final/gest-club/club.php" class="header-nav-link">Clubes</a>
                <a href="/proyecto-final/gest-fixture/fixtures.php" class="header-nav-link">Fixture</a>
                <a href="/proyecto-final/gest-sanciones/sanciones.php" class="header-nav-link">Sanciones</a>
            </div>
        </nav>

        <div class="header-derecha">
            <a href="#" class="header-item">
                <i class="bi bi-bell"></i>
            </a>

            <a href="#" class="header-item">
                <i class="bi bi-person-circle"></i>
                <span></span>
            </a>

            <a href="../index.php" class="header-item">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar sesión</span>
            </a>
        </div>
    </header>
<br><br>
    <div class="users-form">
        <h1>Ingresar un jugador</h1>
        <form action="agregar-jug.php" method="POST">
            <input type="text" name="nombre" placeholder="Nombre">
            <input type="text" name="apellido" placeholder="Apellido">
            <input type="text" name="cedula" placeholder="Cédula">
            <select name="idclub" required>
            <option value="">Seleccionar club</option>
            <?php while ($club = mysqli_fetch_array($queryClub)): ?>
                <option value="<?= $club['idClub'] ?>">
                    <?= $club['nombreClub'] ?>
                </option>
            <?php endwhile; ?>

            </select>
            <input type="date" name="fecha-nacimiento" required>
            <input type="text" name="genero" placeholder="Género" maxlength="1" required>
            <select name="idcategoria" required>
            <option value="">Seleccionar categoría</option>

            <?php while ($categoria = mysqli_fetch_array($queryCategoria)): ?>
                <option value="<?= $categoria['idCategoria'] ?>">
                    <?= $categoria['año'] ?>
                </option>
            <?php endwhile; ?>

            </select>

            <!-- Datos físicos usados para calcular la fuerza peso del jugador. -->
            <input type="number" name="masa" placeholder="Masa (kg)" min="1" max="500" step="0.01" required>
            <input type="number" name="altura" placeholder="Altura (m)" min="0.5" max="2.5" step="0.01" required>
            <input type="number" name="velocidad" placeholder="Velocidad (m/s)" min="0" max="15" step="0.01" required>

            <input type="submit" value="Agregar">
        </form>
    </div>
<br><br>
    <div class="users-table">
        <h2>Jugadores registrados</h2>
        <table>
            <thead>
                <tr>
                    <th>Cédula</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Club</th>
                    <th>Fecha de nacimiento</th>
                    <th>Género</th>
                    <th>Categoría</th>
                    <!-- Magnitudes físicas registradas y resultado de fuerza peso. -->
                    <th>Masa (kg)</th>
                    <th>Altura (m)</th>
                    <th>Velocidad (m/s)</th>
                    <th>Fuerza peso (N)</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_array($query)): ?>
                    <tr>
                        <th><?= $row['cedula'] ?></th>
                        <th><?= $row['nombre'] ?></th>
                        <th><?= $row['apellido'] ?></th>
                        <th><?= $row['nombreClub'] ?></th>
                        <th><?= $row['fechaNacimiento'] ?></th>
                        <th><?= $row['genero'] ?></th>
                        <th><?= $row['idCategoria'] ?></th>
                        <th><?= $row['masa'] ?></th>
                        <th><?= $row['altura'] ?></th>
                        <th><?= $row['velocidad'] ?></th>
                        <th><?= number_format($row['fuerzaPeso'], 2) ?></th>
                        <th><a href="actualizar-jug.php?id=<?= $row['cedula'] ?>" class="users-table--edit">Editar</a></th>
                        <th><a href="eliminar-jug.php?id=<?= $row['cedula'] ?>" class="users-table--delete" >Eliminar</a></th>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>