<?php
// Elimina la sanción indicada por su identificador y regresa al listado.
    include(__DIR__ . "/../../conexion-bd/conexion.php");
    $conexion = connection();

    $idSancion=$_GET["idSancion"];

    $sql="DELETE FROM sancion WHERE idSancion='$idSancion'";
    $query = mysqli_query($conexion, $sql);

    Header("Location: sanciones.php");
    exit();
?>