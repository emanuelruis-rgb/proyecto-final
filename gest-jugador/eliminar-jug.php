<?php

include(__DIR__ . "/../conexion-bd/conexion.php");
$con = connection();

/*
    Un jugador puede tener sanciones o carnets relacionados mediante claves
    foráneas. Por eso primero se eliminan esos registros dependientes y luego
    el jugador, todo dentro de una transacción para evitar borrados parciales.
*/
$id = $_GET["id"] ?? '';

// Se eliminan primero los registros relacionados para respetar las claves foráneas.
//Las claves foráneas (Foreign Keys) son tablas que se relacionan con otra tabla en una base de datos.
mysqli_begin_transaction($con);

try {
    // Las sanciones pertenecientes al jugador no pueden quedar sin jugador asociado.
    $sqlSanciones = mysqli_prepare($con, "DELETE FROM sancion WHERE cedulaJugador = ?");
    mysqli_stmt_bind_param($sqlSanciones, "s", $id);
    mysqli_stmt_execute($sqlSanciones);
    mysqli_stmt_close($sqlSanciones);

    // Los carnets relacionados también dependen de la cédula del jugador.
    $sqlCarnets = mysqli_prepare($con, "DELETE FROM carnet WHERE cedulaJugador = ?");
    mysqli_stmt_bind_param($sqlCarnets, "s", $id);
    mysqli_stmt_execute($sqlCarnets);
    mysqli_stmt_close($sqlCarnets);

    // El jugador se elimina solamente después de borrar sus registros dependientes.
    $sqlJugador = mysqli_prepare($con, "DELETE FROM jugador WHERE cedula = ?");
    mysqli_stmt_bind_param($sqlJugador, "s", $id);
    mysqli_stmt_execute($sqlJugador);
    mysqli_stmt_close($sqlJugador);

    // Se confirman los tres borrados como una sola operación.
    mysqli_commit($con);
    Header("Location: jugador.php");
    exit();
} catch (mysqli_sql_exception $error) {
    // Si algún borrado falla, se conserva toda la información original.
    mysqli_rollback($con);
    die("Error al eliminar el jugador: " . $error->getMessage());
}

?>