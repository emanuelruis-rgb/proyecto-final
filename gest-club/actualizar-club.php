<?php
    include(__DIR__ . "/../conexion-bd/conexion.php");
    $con=connection();

    $id=$_GET['id'];

    $sql="SELECT * FROM club WHERE idClub='$id'";
    $query=mysqli_query($con, $sql);

    $row=mysqli_fetch_array($query);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="/proyecto-final/gest-club/club-style.css" rel="stylesheet">
        <title>Editar club</title>
        
    </head>
    <br><br>
    <body>
        <div class="users-form">
            <form action="editar-club.php" method="POST">
                <input type="hidden" name="idClub" value="<?= $row['idClub']?>">
                <input type="text" name="nombreClub" placeholder="Nombre" value="<?= $row['nombreClub']?>">
                <input type="text" name="contraseñaClub" placeholder="Contraseña" value="<?= $row['contraseñaClub']?>">
                <input type="text" name="nombrePresidente" placeholder="Presidente" value="<?= $row['nombrePresidente']?>">
                <input type="text" name="añoCreacion" placeholder="Año de fundación" value="<?= $row['añoCreacion']?>">
                <input type="text" name="estadio" placeholder="Estadio" value="<?= $row['estadio']?>">

                <input type="submit" value="Actualizar">
            </form>
        </div>
    </body>
</html>