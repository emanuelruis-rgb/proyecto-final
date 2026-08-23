<?php
include(__DIR__ . "/../conexion-bd/conexion.php");
$con = connection();
$query = mysqli_query($con, "SELECT * FROM club");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de clubes</title>
    <link rel="stylesheet" href="/proyecto-final/gest-club/club-style.css">
</head>
<body>

    <header> 
        <nav class="main-nav">
            <a href="/">
                <img src="../img/logo-atrivia.png" alt="Logo" class="logo-empresa">
            </a>
            <div class="nav-links">
                <a href="Jugadores.php" class="header-nav-link">Jugadores</a>
                <a href="#" class="header-nav-link">Clubes</a>
                <a href="fixtures.php" class="header-nav-link active">Fixture</a>
                <a href="#" class="header-nav-link">Sanciones</a>
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
        <h1>Crear club</h1>
        <form action="agregar-club.php" method="POST">
            <input type="text" name="nombre" placeholder="Nombre">
            <input type="password" name="contraseña" placeholder="Contraseña">
            <input type="text" name="presidente" placeholder="Presidente">
            <input type="text" name="año-fundacion" placeholder="Año de fundación">
            <input type="text" name="estadio" placeholder="Estadio">

            <input type="submit" value="Agregar">
        </form>
    </div>
<br><br>
    <div class="users-table">
        <h2>Clubes registrados</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Contraseña</th>
                    <th>Presidente</th>
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
                        <th><a href="actualizar.php?id=<?= $row['idClub'] ?>" class="users-table--edit">Editar</a></th>
                        <th><a href="eliminar-club.php?id=<?= $row['idClub'] ?>" class="users-table--delete" >Eliminar</a></th>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>
</html>