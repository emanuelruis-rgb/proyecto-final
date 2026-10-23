<?php
    session_name('club_session');
    session_start();
    if (!isset($_SESSION['nombre'], $_SESSION['idClub'])) {
        header('Location: /proyecto-final/index.php');
        exit;
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto-final/conexion-bd/conexion.php";
    $conexion = connection();

    $nombreSesionClub = $_SESSION['nombre'] ?? '';
    $idClub = $_SESSION['idClub'];
    $nombreMostrarClub = $nombreSesionClub !== '' ? $nombreSesionClub : 'Club';

    $stmt = mysqli_prepare($conexion, "SELECT nombreClub FROM club WHERE nombreClub = ?");
    mysqli_stmt_bind_param($stmt, "s", $nombreSesionClub);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($fila = mysqli_fetch_assoc($resultado)) {
        $nombreMostrarClub = $fila['nombreClub'];
    }

    //esta parte es para poder meter el nombre del jugador en el recuadro de ingreso del carnet para comodidad del club
    $cedula=$_GET['cedula'];
    $consultaNombreJugador = mysqli_query($conexion, "SELECT * FROM jugador WHERE cedula = $cedula");
    $jugador = mysqli_fetch_assoc($consultaNombreJugador);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carnets</title>
    <link rel="stylesheet" href="../jugador/jugadores-club-style.css">
    <link rel="stylesheet" href="/proyecto-final/include/css/style.css?v=1">
    <link rel="icon" type="image/png" href="../../img/logo-liga/log-liga-b.png">
</head>
<body>
    <header> 
        <nav class="nav-izquierda-container">
            <a href="/proyecto-final/club/indexclub.php">
                <img src="/proyecto-final/img/logo-liga/log-liga-d.png" alt="Logo" class="logo-empresa">
            </a>
            <div class="nav-izquierda-botones-container">
                <!-- aca van los jugadores propios y despues da la opcion de seleccionar los ajenos-->
                <a href="/proyecto-final/club/jugador/jugadores-club.php?tipoClub=propio" class="nav-izquierda-botones">Jugadores</a>
                <a href="/proyecto-final/club/sancion/sanciones.php" class="nav-izquierda-botones">Sanciones</a>
                <a href="/proyecto-final/club/fixture/fixture.php" class="nav-izquierda-botones">Fixture</a>
                <a href="/proyecto-final/club/tabla-posicion/posiciones.php" class="nav-izquierda-botones">Tabla de posiciones</a>
            </div>
        </nav>
        <!-- Identifica el espacio de trabajo sin añadir otra opción al navbar. -->
        </div>
        <div class="user-menu">
            <button class="user-menu-toggle" onclick="toggleUserMenu()">
                <i class="bi bi-person-circle"></i>
                <span class="user-menu-name"><?php echo htmlspecialchars($nombreMostrarClub); ?></span>
            </button>

            <div class="user-menu-dropdown" id="userDropdown">
                <a href="/proyecto-final/uploads/documentos/reglamento.pdf" class="user-menu-item" download>
                    <i class="bi bi-file-earmark-text"></i> Descargar documento
            </a>
            <a href="/proyecto-final/logout.php?rol=club" class="user-menu-item">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar sesión</span>
            </a>
            </div>
        </div>
    </header>
        
    <main>

    <!-- FALTA CUSTOMIZAR TODO ESTO PARA SUBIR EL CARNET -->
    <div class="formulario-container">
        <h1>Subir carnet médico de <?php
            echo $jugador["nombre"]. " " . $jugador["apellido"];
        ?>
        </h1>
        <form action="agregar-carnet.php" method="POST" enctype="multipart/form-data">
            <label for="file" class="texto-formulario">Seleccione el carnet médico</label>
            <!-- campo para seleccionar de la pc directo, el archivo jpeg,pdf,jpg,png que es el carnet -->
            <div class="subir-carnet-container">
                <input type="file" name="carnetSubido" id="carnetSubido" accept=".pdf,.jpg,.jpeg,.png" required>
            </div>
            <!-- campo para seleccionar el vencimiento del carnet -->
             <label for="date" class="texto-formulario">Seleccione la fecha de vencimiento del carnet</label>
            <input type="date" class="fecha-vencimiento-input" name="fecha-vencimiento-carnet" id="fecha-vencimiento-carnet" min="<?= date('Y-m-d') ?>" required>
            <input type="hidden" name="cedulaJugador" value="<?= $cedula ?>">
            <input type="submit" value="Subir" class="boton-subir-carnet">
        </form>
    </div>
    
    </main>
    <?php include_once __DIR__ . '/../../include/footer.php'; ?>
</body>
</html>