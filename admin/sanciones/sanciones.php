<?php
    include_once(__DIR__ . "/../../conexion-bd/conexion.php");

    session_name('admin_session');
    session_start();
    if (!isset($_SESSION['nombre'])) {
        header('Location: /proyecto-final/index.php');
        exit;
    }

    $conexion = connection();

    /*query que busca los clubes para ponerlos en los option de select clubes */
    $queryClubes = mysqli_query($conexion, "SELECT * FROM club");
    /* join que junta las filas de acuerdo a la cédula del jugador y al club al que pertenece */
    $filtroClub      = (int)($_GET['filtro-club'] ?? 0);
$filtroCategoria = (int)($_GET['filtro-categoria'] ?? 0);

// Categorías para el selector del filtro
$categorias = mysqli_fetch_all(
    mysqli_query($conexion, "SELECT idCategoria, `año` FROM categoria ORDER BY `año` DESC"),
    MYSQLI_ASSOC
);

$where  = [];
$tipos  = "";
$params = [];

if ($filtroClub > 0) {
    $where[]  = "c.idClub = ?";
    $tipos   .= "i";
    $params[] = $filtroClub;
}
if ($filtroCategoria > 0) {
    $where[]  = "j.idCategoria = ?";
    $tipos   .= "i";
    $params[] = $filtroCategoria;
}

$sqlSanciones = "
    SELECT s.idSancion, j.cedula, j.nombre, j.apellido, cat.`año` AS categoria,
           c.nombreClub, s.tipo, s.motivo, s.fechaSuspencion
    FROM sancion s
    INNER JOIN jugador j   ON s.cedulaJugador = j.cedula
    INNER JOIN club c      ON j.idClub = c.idClub
    LEFT JOIN categoria cat ON j.idCategoria = cat.idCategoria"
    . ($where ? " WHERE " . implode(" AND ", $where) : "")
    . " ORDER BY s.idSancion DESC";

$stmtSanciones = mysqli_prepare($conexion, $sqlSanciones);
if ($params) {
    mysqli_stmt_bind_param($stmtSanciones, $tipos, ...$params);
}
mysqli_stmt_execute($stmtSanciones);
$querySanciones = mysqli_stmt_get_result($stmtSanciones);

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de sanciones</title>
    <link rel="stylesheet" href="../pagina-principal-admin.css?v=5">
    <link rel="stylesheet" href="sanciones-style.css?v=1">
    <link rel="stylesheet" href="/proyecto-final/include/css/style.css?v=1">
    <link rel="icon" type="image/png" href="../../img/logo-liga/log-liga-b.png">
    <script src="sanciones.js" defer></script>
</head>
<body>
    <header> 
        <nav class="nav-izquierda-container">
            <a href="/proyecto-final/admin/indexadmin.php">
                <img src="/proyecto-final/img/logo-liga/log-liga-b.png" alt="Logo" class="logo-liga">
            </a>
            <div class="nav-izquierda-botones-container">
                <a href="../jugador/jugador.php" class="nav-izquierda-botones">Jugadores</a>
                <a href="../club/club.php" class="nav-izquierda-botones">Clubes</a>
                <a href="../fixture/fixtures.php" class="nav-izquierda-botones">Fixture</a>
                <a href="sanciones.php" class="nav-izquierda-botones active">Sanciones</a>
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

    <div class="formulario-container">
        <h1>Crear sanción</h1>
        <!-- aca el admin pone los datos de la sancion.
         en base al club seleccionado, despliega los jugadores del club y ahi se selecciona,
         autocompleta la cédula en base al jugador. la sancion agregada va a agregar-sancion.php -->
        <form action="agregar-sancion.php" method="POST">
            <!-- se selecciona club, y de aca a la BD va el id club -->
            <select name="id-club-sancion" id="select-club" class="select-formulario" required>
                <option value="" disabled selected>Seleccionar club del sancionado</option>
                <!-- este php itera por los clubes y pone un option mas por cada club, con el value siendo el idclub y
                  $club es el array del club y el fetch array busca cada fila con la query de allá arriba-->
                <?php while ($club = mysqli_fetch_array($queryClubes)): ?>
                    <option value="<?= $club['idClub'] ?>">
                <?= $club['nombreClub'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <select id="select-categoria" class="select-formulario">
                <option value="">Todas las categorías</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?=(int)$cat['idCategoria']?>"><?=htmlspecialchars($cat['año'])?></option>
                <?php endforeach; ?>
            </select>
            <!-- se selecciona jugador de los disponibles en el club, y a la BD va su cedula-->
            <select name="ci-jugador-sancion" id="select-jugador" class="select-formulario" required>
                <option value="" disabled selected>Seleccionar jugador sancionado</option>

            </select>

            <select name="tipo-sancion" class="select-formulario">
                <option value="" disabled selected>Tipo de sanción</option>
                <option value="Amarilla">Tarjeta amarilla</option>
                <option value="Roja">Tarjeta roja</option>
                <option value="Disciplinaria">Sanción disciplinaria</option>
            </select>

            <input type="text" name="motivo-sancion" placeholder="Motivo de la sanción" required>
            <input type="number" name="numero-fechas" placeholder="Cantidad de fechas de suspensión" min="0" required>
            
            <input type="submit" value="Agregar">
        </form>
    </div>
<br><br>

    <div class="tabla">
        <h2>Sanciones registradas</h2>
        <form class="filtros" method="GET" class="filtros-sanciones">
    <select name="filtro-club" onchange="this.form.submit()">
        <option value="0">Todos los clubes</option>
        <?php mysqli_data_seek($queryClubes, 0); // el while de arriba ya recorrió el resultado ?>
        <?php while ($club = mysqli_fetch_array($queryClubes)): ?>
            <option value="<?= $club['idClub'] ?>" <?= $club['idClub'] == $filtroClub ? 'selected' : '' ?>>
                <?= htmlspecialchars($club['nombreClub']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <select name="filtro-categoria" onchange="this.form.submit()">
    <option value="0">Todas las categorías</option>
    <?php foreach ($categorias as $cat): ?>
        <option value="<?= $cat['idCategoria'] ?>" <?= $cat['idCategoria'] == $filtroCategoria ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['año']) ?>
        </option>
    <?php endforeach; ?>
</select>

    <?php if ($filtroClub || $filtroCategoria): ?>
    <a href="sanciones.php"class="limpiar-filtros">Limpiar filtros</a>
<?php endif; ?>
</form>
        <table>
            <thead>
                <tr>
                    <!-- YA ESTAN CAMBIADOS LOS NOMBRES AHORA HAY QUE ADAPTAR EL FORMULARIO DE INTRODUCCION PARA Sanciones
                     EL SISTEMA DE RECUPERACION PARA PONER LAS SANCIONES EN LA TABLA INFERIOR LO HAGO DE CERO -->
                    <th>CI</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Club</th>
                    <th>Categoría</th>
                    <th>Tipo de Sanción</th>
                    <th>Motivo</th>
                    <th>Fechas suspensión</th>
                    <!-- Celdas vacías para conservar el ancho de los botones. -->
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <!-- este while itera por las sanciones con el join de allá arriba
                 y $sancion es una sancion invidual -->
                <?php while ($sancion = mysqli_fetch_array($querySanciones)): ?>
            <tr>
                <!-- cada uno de estos td define una columna, y busca una columna en la bd, en orden -->
                <td><?= $sancion['cedula'] ?></td>
                <td><?= $sancion['nombre'] ?></td>
                <td><?= $sancion['apellido'] ?></td>
                <td><?= $sancion['nombreClub'] ?></td>
                <td><?= htmlspecialchars($sancion['categoria'] ?? '-') ?></td> 
                <td><?= $sancion['tipo'] ?></td>
                <td><?= $sancion['motivo'] ?></td>
                <td><?= $sancion['fechaSuspencion'] ?></td>
                <th><a href="editar-sancion.php?idSancion=<?= $sancion['idSancion'] ?>" class="tabla--edit"><i class="bi bi-pencil-square"></i></a></th>
                <th><a href="eliminar-sancion.php?idSancion=<?= $sancion['idSancion'] ?>" class="tabla--delete" ><i class="bi bi-trash3-fill"></i></a></th>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php include_once __DIR__ . '/../../include/footer.php'; ?>
</body>
</html>
