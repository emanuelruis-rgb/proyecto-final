<?php
// Carga los clubes y opciones de categoría/competición para el panel de gestión.
include(__DIR__ . "/../../conexion-bd/conexion.php");
$con = connection();

$idCategoriaFiltro = (int)($_GET['categoria'] ?? 0);
$idCompeticionFiltro = (int)($_GET['competicion'] ?? 0);
$busqueda = trim($_GET['buscar'] ?? '');

$filtros = [];

if ($busqueda !== '') {
    $busquedaSQL = mysqli_real_escape_string($con, $busqueda);
    $filtros[] = "club.nombreClub LIKE '%$busquedaSQL%'";
}

if ($idCategoriaFiltro > 0) {
    $filtros[] = "EXISTS (
        SELECT 1
        FROM club_categoria cc
        WHERE cc.idClub = club.idClub
        AND cc.idCategoria = $idCategoriaFiltro
    )";
}

if ($idCompeticionFiltro > 0) {
    $filtros[] = "EXISTS (
        SELECT 1
        FROM club_competicion cp
        WHERE cp.idClub = club.idClub
        AND cp.idCompeticion = $idCompeticionFiltro
    )";
}

$where = count($filtros) > 0 ? "WHERE " . implode(" AND ", $filtros): "";
$query = mysqli_query($con, "SELECT * FROM club $where ORDER BY nombreClub ASC");

$categorias    = mysqli_fetch_all(mysqli_query($con, "SELECT idCategoria, año FROM categoria ORDER BY año"), MYSQLI_ASSOC);
$competiciones = mysqli_fetch_all(mysqli_query($con, "SELECT idCompeticion, nombreCompeticion FROM competicion ORDER BY nombreCompeticion"), MYSQLI_ASSOC);

session_name('admin_session');
session_start();
if (!isset($_SESSION['nombre'])) {
    header('Location: /proyecto-final/index.php');
    exit;
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/proyecto-final/conexion-bd/conexion.php";
$conexion = connection();

$nombreSesion = $_SESSION['nombre'];
$nombreMostrar = $nombreSesion; // valor por defecto, por si la consulta no encuentra nada

// Consulta el nombre de presentación asociado a la sesión del administrador.
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
    <link rel="stylesheet" href="../pagina-principal-admin.css?v=5">
    <link rel="stylesheet" href="club-style.css?v=2">
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
                <a href="../jugador/jugador.php" class="nav-izquierda-botones">Jugadores</a>
                <a href="club.php" class="nav-izquierda-botones active">Clubes</a>
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
        <h1>Crear club</h1>
        <form action="agregar-club.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="nombre" placeholder="Nombre">
            <input type="password" name="contraseña" placeholder="Contraseña">
            <input type="text" name="presidente" placeholder="Presidente">
            <input type="text" name="año-fundacion" placeholder="Año de fundación">
            <input type="text" name="estadio" placeholder="Estadio">
            <label for="logoClub">Logo del club:</label>
            <input type="file" name="logoClub" id="logoClub" accept="image/png, image/jpeg, image/webp">
            <fieldset class="categorias-club">
                <legend>Categorías que integra</legend>
                <?php foreach ($categorias as $cat): ?>
                    <label>
                        <input type="checkbox" name="categorias[]" value="<?= (int)$cat['idCategoria'] ?>">
                        <?= htmlspecialchars($cat['año']) ?>
                    </label>
                <?php endforeach; ?>
            </fieldset>
            
            <fieldset class="competiciones-club">
                <legend>Competiciones en las que participa</legend>
                <?php foreach ($competiciones as $comp): ?>
                    <label>
                        <input type="checkbox" name="competiciones[]" value="<?= (int)$comp['idCompeticion'] ?>">
                        <?= htmlspecialchars($comp['nombreCompeticion']) ?>
                </label>
                <?php endforeach; ?>
                </fieldset>
            <input type="submit" value="Agregar">
        </form>
    </div>
<br><br>
    <div class="users-table">
        <h2>Clubes registrados</h2>
        <form method="GET" class="filtros">

    <input
        type="text"
        id="buscador"
        name="buscar"
        placeholder="Buscar club..."
        value="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>"
    >
    <button type="submit">Buscar</button>
    <select name="categoria" onchange="this.form.submit()">
        <option value="0">Todas las categorías</option>

        <?php foreach ($categorias as $cat): ?>
            <option
                value="<?= (int)$cat['idCategoria'] ?>"
                <?= $idCategoriaFiltro === (int)$cat['idCategoria'] ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($cat['año']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="competicion" onchange="this.form.submit()">
        <option value="0">Todas las competiciones</option>

        <?php foreach ($competiciones as $comp): ?>
            <option
                value="<?= (int)$comp['idCompeticion'] ?>"
                <?= $idCompeticionFiltro === (int)$comp['idCompeticion'] ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($comp['nombreCompeticion']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <a href="club.php" class="limpiar-filtros">Limpiar filtros</a>

</form>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Presidente</th>
                    <th>Año de fundación</th>
                    <th>Estadio</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_array($query)): ?>
                    <tr class="<?= $row['activo'] ? '':'club-inactivo'?>">
                        <td><?= htmlspecialchars($row['nombreClub']) ?></td>
                        <td><?= htmlspecialchars($row['nombrePresidente']) ?></td>
                        <td><?= $row['añoCreacion'] ?></td>
                        <td><?= htmlspecialchars($row['estadio']) ?></td>
                        <td>
                            <form action="cambiar-estado-club.php" method="POST" class="form-estado">
                                <input type="hidden" name="id" value="<?=(int)$row['idClub']?>">
                                <button type="submit" class="btn-estado <?= $row['activo'] ? 'activo':'inactivo'?>"
                                    title="<?= $row['activo'] ? 'Desactivar club' : 'Activar club' ?>">
                                    <?= $row['activo'] ? 'Activo' : 'Inactivo' ?>
                                </button> 
                            </form>
                        </td>
                        <td><a href="actualizar-club.php?id=<?= $row['idClub'] ?>" class="users-table--edit"><i class="bi bi-pencil-square"></i></a></td>
                        <td><a href="eliminar-club.php?id=<?= $row['idClub'] ?>" class="users-table--delete" ><i class="bi bi-trash3-fill"></i></a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php include_once __DIR__ . '/../../include/footer.php'; ?>
    <script src="/proyecto-final/include/buscador.js"></script>
</body>
</html>
