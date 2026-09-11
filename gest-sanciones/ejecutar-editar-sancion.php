<?php 
    include(__DIR__ . "/../conexion-bd/conexion.php");
    $conexion = connection();
    /* este archivo solo manda lo recibido de editar-sancion.php a la bd. edita todo menos la id 
    de la sancion, editar-sancion.php es solo la interfaz */
    $idSancion = $_POST["id-sancion"];
    $clubSancion = $_POST["id-club-sancion"];
    $cedulaJugador = $_POST["ci-jugador-sancion"];
    $tipo = $_POST["tipo-sancion"];
    $motivo = $_POST["motivo-sancion"];
    $fechasSuspension = $_POST["numero-fechas"];

    $sql = "UPDATE sancion
        SET clubSancion = '$clubSancion',
            cedulaJugador = '$cedulaJugador',
            tipo = '$tipo',
            motivo = '$motivo',
            fechaSuspencion = '$fechasSuspension'
        WHERE idSancion = '$idSancion'";

    $queryEditarSancion = mysqli_query($conexion, $sql);

    if (!$queryEditarSancion) {
        die("No se pudieron guardar los cambios: " . mysqli_error($conexion));
    } else {
        echo "se modifico correctamente";
    }
    
    header("Location: sanciones.php");
    exit();
?>