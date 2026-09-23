<?php
include(__DIR__ . "/../conexion-bd/conexion.php");
$con = connection();
$query = mysqli_query($con, "SELECT * FROM club");

session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto-final/conexion-bd/conexion.php";
$conexion = connection();

$nombreSesion = $_SESSION['nombre'];
$nombreMostrar = $nombreSesion; // valor por defecto, por si la consulta no encuentra nada

$stmt = mysqli_prepare($conexion, "SELECT nombreUsuario FROM administrador WHERE nombreUsuario = ?");
mysqli_stmt_bind_param($stmt, "s", $nombreSesion);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if ($fila = mysqli_fetch_assoc($resultado)) {
    $nombreMostrar = $fila['nombreUsuario'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de clubes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/proyecto-final/gest-club/club-style.css">
    <link rel="icon" type="image/png" href="../img/logo-liga/log-liga-b.png">
</head>
<body>

    <header> 
        <nav class="main-nav">
            <a href="/proyecto-final/admin/indexadmin.php">
                <img src="/proyecto-final/img/logo-liga/log-liga-b.png" alt="Logo" class="logo-liga">
            </a>
            <div class="nav-links">
                <a href="/proyecto-final/gest-jugador/jugador.php" class="header-nav-link">Jugadores</a>
                <a href="/proyecto-final/gest-club/club.php" class="header-nav-link active">Clubes</a>
                <a href="/proyecto-final/gest-fixture/fixtures.php" class="header-nav-link">Fixture</a>                
                <a href="/proyecto-final/gest-sanciones/sanciones.php" class="header-nav-link">Sanciones</a>
            </div>
        </nav>

        <script>
        function toggleUserMenu() {
        document.getElementById('userDropdown').classList.toggle('show'); //busca el elemento con la clase UserDropdown y activa/desactiva el menu
        }

        document.addEventListener('click', function(event) {
        const menu = document.querySelector('.user-menu');
        const dropdown = document.getElementById('userDropdown');
        if (!menu.contains(event.target)) {
            dropdown.classList.remove('show');
            }
        });
        </script>

        <div class="user-menu">
            <button class="user-menu-toggle" onclick="toggleUserMenu()">
                <i class="bi bi-person-circle"></i>
                <span class="user-menu-name"><?php echo htmlspecialchars($nombreMostrar); ?></span>
            </button>

            <div class="user-menu-dropdown" id="userDropdown">
                <a href="documento.php" class="user-menu-item">
                    <i class="bi bi-person-badge"></i> Mi perfil
            </a>
            <a href="/proyecto-final/index.php" class="user-menu-item">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar sesión</span>
            </a>
            </div>
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
                        <th><a href="actualizar-club.php?id=<?= $row['idClub'] ?>" class="users-table--edit">Editar</a></th>
                        <th><a href="eliminar-club.php?id=<?= $row['idClub'] ?>" class="users-table--delete" >Eliminar</a></th>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>
</html>