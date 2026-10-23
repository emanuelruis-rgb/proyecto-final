<?php
require (__DIR__ . "/../../conexion-bd/conexion.php");

$conexion = connection();

$carpetaCarnets = __DIR__ . "/carnets/";
// Datos necesarios para que funcione esto y para que se ingresen los registros en la bd
$cedula = $_POST['cedulaJugador'];
//fecha de hoy
$fechaSubida = date('Y-m-d');
$fechaVencimiento = $_POST['fecha-vencimiento-carnet'];

//EMPIEZAN CHEQUEOS DE SEGURIDAD
//verificar si hubo errores o no en la subida del archivo. un 0 significa que no hubo error
if ($_FILES['carnetSubido']['error'] != 0) {
    echo "Error al subir el archivo. Vuelva a la página anterior e intente de nuevo.";
    header("Location: /proyecto-final/club/jugador/jugadores-club.php");
    exit;
}

// Crear un objeto finfo para detectar el tipo de contenido del archivo
$finfo = new finfo(FILEINFO_MIME_TYPE);

//verifica el tipo de archivo subido usando file()
$tipoArchivo = $finfo->file($_FILES['carnetSubido']['tmp_name']);

// verifica el tipo de archivo, si no es permitido, corta el programa
if ($tipoArchivo == "image/jpeg" || $tipoArchivo == "image/png" || $tipoArchivo == "application/pdf") {
    echo "Tipo de archivo permitido";
} else {
    echo "Tipo de archivo no permitido";
    header("Location: /proyecto-final/club/jugador/jugadores-club.php");
    exit;
}
//terminan CHEQUEOS DE SEGURIDAD


// esto usa pathinfo para, usando el nombre del carnet subido, verificar su extension.
//basicamente recibe el path del carnet subido(con 'name') y busca solo su extension
$extensionArchivo = pathinfo($_FILES['carnetSubido']['name'], PATHINFO_EXTENSION);

//con la extension guardada en la linea pasada, concatena la ci con esa extension. queda ci.extension
$nombreArchivoFinal = $cedula . "." . $extensionArchivo;

// ruta destino para subir el archivo. concatena directorio actual, mas /carnets, mas el nombre del archiov final
$rutaDestino = __DIR__ . "/carnets/" . $nombreArchivoFinal;



// Comprobamos si ya existe un carnet para este jugador
$consultaExiste = mysqli_prepare($conexion,
    "SELECT idCarnet FROM carnet WHERE cedulaJugador = ?");

mysqli_stmt_bind_param($consultaExiste, "s", $cedula);
mysqli_stmt_execute($consultaExiste);
//en resultado se ve si ya existe un carnet.
$resultado = mysqli_stmt_get_result($consultaExiste);


//PREPARACIÓN DE CONSULTAS
// Si ya existe un carnet, actualizamos sus datos, con update. 
// se prepara la consulta, sea insert o update, con los mismos nombres($querySubirCarnet) para ejecutarse una vez después.
if (mysqli_num_rows($resultado) > 0) {
    // Si ya hay carnet se usa update
    $querySubirCarnet = mysqli_prepare($conexion,
        "UPDATE carnet
        SET fechaSubida = ?, fechaVencimiento = ?, ruta = ?
        WHERE cedulaJugador = ?");

    mysqli_stmt_bind_param(
        $querySubirCarnet,
        "ssss",
        $fechaSubida,
        $fechaVencimiento,
        $rutaDestino,
        $cedula
    );

} else {
    // Si no existe un carnet, se usa insert
    $querySubirCarnet = mysqli_prepare($conexion,
        "INSERT INTO carnet (fechaSubida, fechaVencimiento, cedulaJugador, ruta)
        VALUES (?, ?, ?, ?)");

    mysqli_stmt_bind_param(
        $querySubirCarnet,
        "ssss",
        $fechaSubida,
        $fechaVencimiento,
        $cedula,
        $rutaDestino
    );
}


// SUBIDA DEL ARCHIVO

// move_uploaded_file mueve el carnet subido con su tmp_name, es el nombre temporal de su ruta temporal,
//usando esa ruta temporal, sube de ahí, hacia rutaDestino

if (move_uploaded_file($_FILES['carnetSubido']['tmp_name'], $rutaDestino)) {
    // Ejecuta la consulta para subir los datos del carnet a la bd y verifica si salió bien
    if (mysqli_stmt_execute($querySubirCarnet)) {

        //si fue exitosa la ejecucion de la query de subir carnet, borramos archivos anteriores.
        $archivosAnteriores = glob($carpetaCarnets . $cedula . '.*');

        foreach ($archivosAnteriores as $archivo) {
            //si el archivo viejo es distinto al recien subido, se borra.
            if ($archivo != $rutaDestino) {
                unlink($archivo);
            }
        }

        mysqli_stmt_close($querySubirCarnet);
        mysqli_stmt_close($consultaExiste);
        header("Location: /proyecto-final/club/jugador/jugadores-club.php");
        exit;
    } else {
        echo "Error al guardar los datos del carnet en la base de datos";
        mysqli_stmt_close($querySubirCarnet);
        mysqli_stmt_close($consultaExiste);
        exit;
    }
} else {
    echo "Error al guardar el carnet";
    header("Location: /proyecto-final/club/jugador/jugadores-club.php");
    exit;
}
?>