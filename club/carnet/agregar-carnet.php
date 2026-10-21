<?php
echo "este es agregar-carnet.php";
$carpetaCarnets = __DIR__ . "/carnets/";
$cedula = $_POST['cedulaJugador'];
echo $cedula;

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


// si cedulaJugador no es vacio, busca archivos anteriores, concatenando la ruta de carnets,
//con la ci del jugador, y cualquier extension.
if (!empty($cedula)) {
    $archivosAnteriores = glob($carpetaCarnets . $cedula . '.*');
    foreach ($archivosAnteriores as $archivo) {
        //Borra cualquier archivo que tenga: la ruta de carpeta carnets, la ci, y cualquier extension.
        unlink($archivo);
    } 
}

//esto sube el archivo, despues de haber verificado su seguridad, establecido el nombre del carnet nuevo,
// y borrado los archivos viejos
// move_uploaded_file mueve el carnet subido con su tmp_name, es el nombre temporal de su ruta temporal,
//usando esa ruta temporal, sube de ahí, hacia rutaDestino
if (move_uploaded_file($_FILES['carnetSubido']['tmp_name'], $rutaDestino)) {
    echo "Carnet subido correctamente";
    header("Location: /proyecto-final/club/jugador/jugadores-club.php");
    exit;
} else {
    echo "Error al guardar el carnet";
    header("Location: /proyecto-final/club/jugador/jugadores-club.php");
    exit;
}


// QUE PUEDAN SUBIR LOS CARNETS DE SOLAMENTE SU CLUB.
//  QUE PUEDAN VER SOLO LOS CARNET AJENOS.
// QUE EL VER CARNET TE MUESTRE EL CARNET YA EXISTENTE CON LA CI DEL JUGADOR.
//REVISAR QUE PASA SI SUBO UN CARNET YA EXISTENTE EN CARNETS.

?>