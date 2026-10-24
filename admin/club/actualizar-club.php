<?php
    // Busca el club solicitado para precargar el formulario de edición.
    include(__DIR__ . "/../../conexion-bd/conexion.php");
    $con = connection();

    $id = (int)$_GET['id'];

    $query = mysqli_query($con, "SELECT * FROM club WHERE idClub = '$id'");
    $row = mysqli_fetch_array($query);

    // Todas las categorías y competiciones para mostrar como casillas
    $categorias    = mysqli_query($con, "SELECT idCategoria, año FROM categoria ORDER BY año");
    $competiciones = mysqli_query($con, "SELECT idCompeticion, nombreCompeticion FROM competicion ORDER BY nombreCompeticion");

    // Categorías que el club ya tiene (para dejarlas marcadas)
    $catsClub = [];
    $resultado = mysqli_query($con, "SELECT idCategoria FROM club_categoria WHERE idClub = $id");
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $catsClub[] = $fila['idCategoria'];
    }

    // Competiciones que el club ya tiene (para dejarlas marcadas)
    $compsClub = [];
    $resultado = mysqli_query($con, "SELECT idCompeticion FROM club_competicion WHERE idClub = $id");
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $compsClub[] = $fila['idCompeticion'];
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/proyecto-final/admin/pagina-principal-admin.css?v=6">
        <link href="/proyecto-final/admin/club/club-style.css?v=2" rel="stylesheet">
        <link rel="icon" type="image/png" href="../../img/logo-liga/log-liga-b.png">
        <title>Editar club</title>
    </head>
    <br><br>
    <body>
        <div class="users-form">
            <form action="editar-club.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="idClub" value="<?= $row['idClub'] ?>">
                <input type="text" name="nombreClub" placeholder="Nombre" value="<?= $row['nombreClub'] ?>">
                <input type="text" name="contraseñaClub" placeholder="Contraseña" value="<?= $row['contraseñaClub'] ?>">
                <input type="text" name="nombrePresidente" placeholder="Presidente" value="<?= $row['nombrePresidente'] ?>">
                <input type="text" name="añoCreacion" placeholder="Año de fundación" value="<?= $row['añoCreacion'] ?>">
                <input type="text" name="estadio" placeholder="Estadio" value="<?= $row['estadio'] ?>">

                <?php if (!empty($row['logoClub'])): ?>
    <img
        src="/proyecto-final/<?= htmlspecialchars($row['logoClub'], ENT_QUOTES, 'UTF-8') ?>"
        width="100"
        alt="Logo del club"
        style="object-fit: contain;"
    >
<?php else: ?>
    <p>Este club no tiene logo.</p>
<?php endif; ?>

                <label for="logoClub">Cambiar logo (opcional):</label>
                <input type="file" name="logoClub" id="logoClub" accept="image/png, image/jpeg, image/webp">

                <fieldset class="categorias-club">
                    <legend>Categorías que integra</legend>
                    <?php while ($cat = mysqli_fetch_assoc($categorias)): ?>
                        <label>
                            <input type="checkbox" name="categorias[]" value="<?= $cat['idCategoria'] ?>"
                                <?= in_array($cat['idCategoria'], $catsClub) ? 'checked' : '' ?>>
                            <?= $cat['año'] ?>
                        </label>
                    <?php endwhile; ?>
                </fieldset>

                <fieldset class="categorias-club">
                    <legend>Competiciones en las que participa</legend>
                    <?php while ($comp = mysqli_fetch_assoc($competiciones)): ?>
                        <label>
                            <input type="checkbox" name="competiciones[]" value="<?= $comp['idCompeticion'] ?>"
                                <?= in_array($comp['idCompeticion'], $compsClub) ? 'checked' : '' ?>>
                            <?= $comp['nombreCompeticion'] ?>
                        </label>
                    <?php endwhile; ?>
                </fieldset>

                <input type="submit" value="Actualizar">
            </form>
        </div>
    </body>
</html>