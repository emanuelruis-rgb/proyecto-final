<?php

include(__DIR__ . "/../conexion-bd/conexion.php");
$con = connection();

$cedula = $_GET['id'];

$sql = "SELECT * FROM jugador WHERE cedula='$cedula'";
$query = mysqli_query($con, $sql);

$row = mysqli_fetch_array($query);
$queryClub = mysqli_query($con, "SELECT * FROM club");
$queryCategoria = mysqli_query($con, "SELECT * FROM categoria");

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/proyecto-final/gest-jugador/jug-style.css" rel="stylesheet">
    <title>Editar jugador</title>
</head>
<body>
    <div class="users-form">
        <h1>Editar jugador</h1>
        <form action="editar-jug.php" method="POST">
            <input 
                type="hidden" 
                name="cedula" 
                value="<?= $row['cedula'] ?>"
            >
            <input 
                type="text" 
                name="nombre" 
                placeholder="Nombre"
                value="<?= $row['nombre'] ?>"
                required
            >
            <input 
                type="text" 
                name="apellido" 
                placeholder="Apellido"
                value="<?= $row['apellido'] ?>"
                required
            >
            <select name="idclub" required>
                <option value="">
                    Seleccionar club
                </option>
                <?php while ($club = mysqli_fetch_array($queryClub)): ?>
                    <option 
                        value="<?= $club['idClub'] ?>"
                        <?= ($club['idClub'] == $row['idClub']) ? 'selected' : '' ?>
                    >
                        <?= $club['nombreClub'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <!-- Se editan las magnitudes físicas con las mismas unidades y límites del alta. -->
            <input 
                type="date" 
                name="fechaNacimiento"
                value="<?= $row['fechaNacimiento'] ?>"
                required
            >
            <select name="genero" required>

                <option value="">
                </option>

                <option 
                    value="M"
                    <?= ($row['genero'] == 'M') ? 'selected' : '' ?>
                >
                    M
                </option>

                <option 
                    value="F"
                    <?= ($row['genero'] == 'F') ? 'selected' : '' ?>
                >
                    F
                </option>

            </select>
            <select name="idcategoria" required>
                <option value="">
                </option>
                <?php while ($categoria = mysqli_fetch_array($queryCategoria)): ?>
                    <option 
                        value="<?= $categoria['idCategoria'] ?>"
                        <?= ($categoria['idCategoria'] == $row['idCategoria']) ? 'selected' : '' ?>
                    >
                        <?= $categoria['año'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <input
                type="number"
                name="masa"
                placeholder="Masa (kg)"
                value="<?= $row['masa'] ?>"
                min="1"
                max="500"
                step="0.01"
                required
            >
            <input
                type="number"
                name="altura"
                placeholder="Altura (m)"
                value="<?= $row['altura'] ?>"
                min="0.5"
                max="2.5"
                step="0.01"
                required
            >
            <input
                type="number"
                name="velocidad"
                placeholder="Velocidad (m/s)"
                value="<?= $row['velocidad'] ?>"
                min="0"
                max="15"
                step="0.01"
                required
            >
            <input type="submit" value="Actualizar">
        </form>
    </div>
</body>
</html>