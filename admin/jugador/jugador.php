<?php
session_name('admin_session');
session_start();

if (!isset($_SESSION['nombre'])) {
    header('Location: /proyecto-final/index.php');
    exit;
}

require_once(__DIR__ . "/../../conexion-bd/conexion.php");

$conexion = connection();
$con = $conexion;

$categorias = mysqli_fetch_all(mysqli_query($con, "SELECT idCategoria, `año` FROM categoria ORDER BY `año` DESC"), MYSQLI_ASSOC);
/* esta query hace join de jugador y club para poder no solo tener todos los datos del jugador
sino que también los del club. asi ponemos, en vez de idclub, ponemos nombreClub */
/*
    Cálculo de la fuerza peso:
    P = m x g, donde m es la masa en kg y g = 9,80665 m/s².
    El resultado se expresa en Newtons (N), por eso se muestra como fuerzaPeso.
*/
$idCategoriaFiltro = (int)($_GET['categoria'] ?? 0);
$idClubFiltro = (int)($_GET['club'] ?? 0);
$busqueda = trim($_GET['buscar'] ?? '');
$filtros = [];

if ($idCategoriaFiltro > 0) {
    $filtros[] = "jugador.idCategoria = $idCategoriaFiltro";
}

if ($idClubFiltro > 0) {
    $filtros[] = "jugador.idClub = $idClubFiltro";
}

if ($busqueda !== '') {
    $busquedaSQL = mysqli_real_escape_string($con, $busqueda);

    $filtros[] = "(
        jugador.nombre LIKE '%$busquedaSQL%'
        OR jugador.apellido LIKE '%$busquedaSQL%'
        OR jugador.cedula LIKE '%$busquedaSQL%'
    )";
}

$where = count($filtros) > 0
    ? "WHERE " . implode(" AND ", $filtros)
    : "";


//la query para poder encontrar todos los datos necesarios para mostrar en la tabla.
//en la parte de carnet, lo que está entre paréntesis se ejecuta, y busca en base a cedula del jugador
// y a la cedulaJugador de carnet, y ahí busca fechaVencimiento
$query = mysqli_query($con, "
    SELECT
        jugador.idClub,
        club.nombreClub,
        jugador.cedula,
        (SELECT carnet.fechaVencimiento
        FROM carnet
        WHERE carnet.cedulaJugador = jugador.cedula
        LIMIT 1) AS fechaVencimiento,
        jugador.nombre,
        jugador.apellido,
        jugador.fechaNacimiento,
        jugador.genero,
        jugador.idCategoria,
        categoria.`año` AS añoCategoria,
        jugador.masa,
        jugador.altura,
        jugador.velocidad,
        jugador.masa * 9.80665 AS fuerzaPeso
    FROM jugador
    INNER JOIN club
        ON jugador.idClub = club.idClub
    INNER JOIN categoria
        ON jugador.idCategoria = categoria.idCategoria
    $where
");
$queryCategoria = mysqli_query($con, "SELECT * FROM categoria");
$queryClub = mysqli_query($con, "SELECT * FROM club");

$errorCedula=$_SESSION['errorCedula'] ?? '';
unset($_SESSION['errorCedula']);



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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de jugadores</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../pagina-principal-admin.css?v=5">
    <link rel="stylesheet" href="jug-style.css?v=1">
    <link rel="stylesheet" href="/proyecto-final/include/css/style.css?v=1">
    <link rel="icon" type="image/png" href="../../img/logo-liga/log-liga-b.png">
</head>
<body>
    
    <header> 
        <nav class="nav-izquierda-container">
            <a href="/proyecto-final/admin/indexadmin.php">
                <img src="/proyecto-final/img/logo-liga/log-liga-b.png" alt="Logo" class="logo-liga">
            </a>
            <div class="nav-izquierda-botones-container">
                <a href="jugador.php" class="nav-izquierda-botones active">Jugadores</a>
                <a href="../club/club.php" class="nav-izquierda-botones">Clubes</a>
                <a href="../fixture/fixtures.php" class="nav-izquierda-botones">Fixture</a>                
                <a href="../sanciones/sanciones.php" class="nav-izquierda-botones">Sanciones</a>
                <a href="../fixture/posiciones.php" class="nav-izquierda-botones">Tabla de posiciones</a>
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
                <a href="/proyecto-final/gest-documentos/documento.php" class="user-menu-item">
                    <i class="bi bi-file-earmark-text"></i> Subir documento
            </a>
            <a href="/proyecto-final/logout.php?rol=admin" class="user-menu-item">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar sesión</span>
            </a>
            </div>
        </div>
    </header>
<br><br>
    <div class="users-form">
        <h1>Ingresar un jugador</h1>
        <form action="agregar-jug.php" method="POST">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="text" name="apellido" placeholder="Apellido" required>
            <input type="text" name="cedula" placeholder="Cédula" required>
            <?php if($errorCedula): ?>
                <small style="color:red;"><?=htmlspecialchars($errorCedula) ?></small>
            <?php endif; ?>
            <select name="idclub" required>
            <option value="">Seleccionar club</option>
            <?php while ($club = mysqli_fetch_array($queryClub)): ?>
                <option value="<?= $club['idClub'] ?>">
                    <?= $club['nombreClub'] ?>
                </option>
            <?php endwhile; ?>

            </select>
            <input type="date" name="fecha-nacimiento" id="fecha-nacimiento" min="2013-01-01" max="2019-01-01" required>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    function seleccionarCategoria() {
                        const fecha = document.getElementById('fecha-nacimiento').value;
                        const selectCategoria = document.getElementById('idcategoria');
                        const genero = document.getElementById('genero').value;
                        let idCategoria = "";

                        if(genero === "F") {
                            idCategoria = "9";
                        } else if (fecha) {
                            const anio = parseInt(fecha.split('-')[0]);

                            switch (anio) {
                                case 2013: idCategoria = "1"; break;
                                case 2014: idCategoria = "2"; break;
                                case 2015: idCategoria = "3"; break;
                                case 2016: idCategoria = "4"; break;
                                case 2017: idCategoria = "5"; break;
                                case 2018: idCategoria = "6"; break;
                                case 2019: idCategoria = "7"; break;
                                case 2020: idCategoria = "8"; break;
                            }
                        }
                        selectCategoria.value = idCategoria;
                    }
                    document.getElementById('fecha-nacimiento').addEventListener('change', seleccionarCategoria);
                    document.getElementById('genero').addEventListener('change', seleccionarCategoria);
                });
            </script>
            <select name="genero" id="genero" required>
                <option value="" disabled selected>Seleccionar género</option>
                <option value="H">Masculino</option>
                <option value="F">Femenino</option>
            </select>
            <select name="idcategoria" id="idcategoria" required>
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
        <form method="GET" class="filtros">
        <input
            type="text"
            name="buscar"
            id="buscador"
            placeholder="Buscar por nombre, apellido o cédula..."
            value="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>"
        >

        <button type="submit">Buscar</button>
        <select name="club" onchange="this.form.submit()">
    <option value="0">Todos los clubes</option>

    <?php
    $clubesFiltro = mysqli_query($con, "SELECT idClub, nombreClub FROM club ORDER BY nombreClub ASC");
    while ($club = mysqli_fetch_assoc($clubesFiltro)):
    ?>
        <option
            value="<?= (int)$club['idClub'] ?>"
            <?= (int)$club['idClub'] === $idClubFiltro ? 'selected' : '' ?>>
            <?= htmlspecialchars($club['nombreClub']) ?>
        </option>
    <?php endwhile; ?>
</select>

      <select name="categoria" onchange="this.form.submit()">
        <option value="0">Todas las categorías</option>
        <?php foreach ($categorias as $cat): ?>
          <option value="<?= (int)$cat['idCategoria'] ?>" <?= $cat['idCategoria'] == $idCategoriaFiltro ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['año']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <a href="jugador.php" class="limpiar-filtros">Limpiar filtros</a>
    </form>
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
                    <th>Editar</th>
                    <th>Eliminar</th>
                    <th>Carnets
                    </th>
                    <th>Vencido</th>
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
                        <td><?= htmlspecialchars((string) $row['añoCategoria']) ?></td>
                        <th><?= $row['masa'] ?></th>
                        <th><?= $row['altura'] ?></th>
                        <th><?= $row['velocidad'] ?></th>
                        <th><?= number_format($row['fuerzaPeso'], 2) ?></th>
                        <th><a href="actualizar-jug.php?id=<?= $row['cedula'] ?>" class="users-table--edit"><i class="bi bi-pencil-square"></i></a></th>
                        <th><a href="eliminar-jug.php?id=<?= $row['cedula'] ?>" class="users-table--delete" ><i class="bi bi-trash3-fill"></i></a></th>
                        <td>
                            <!-- esto genera el boton para poder ver el carnet. urlencode es para generar un link, formatea -->
                            <a href="/proyecto-final/club/carnet/ver-carnet.php?cedula=<?= urlencode($row['cedula']) ?>"
                            class="botones-carnet">Ver carnet</a>
                        </td>
                        <td>
                            <!-- esto busca con un if corto. si hay fechaVencimiento, y fecha vencimiento es anterior al dìa de hoy,
                            muestra vencido, en culaquier otro caso, muestra no-->
                            <?php
                                if (!empty($row['fechaVencimiento']) && $row['fechaVencimiento'] < date('Y-m-d')) {
                                    echo 'Vencido';
                                } else {
                                    echo 'No';
                                }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </div>
        <?php include_once __DIR__ . '/../../include/footer.php'; ?>
        <script src="/proyecto-final/include/buscador.js"></script>
</body>
</html>
