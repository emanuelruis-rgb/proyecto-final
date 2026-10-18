<?php

// Abre y devuelve la conexión compartida con la base de datos de la liga.
function connection(){
    $host = "localhost";
    $user = "root";
    $pass = "";

    $bd = "liga-de-futbol";

    $connect=mysqli_connect($host, $user, $pass);

    mysqli_select_db($connect, $bd);

    return $connect;

}


?>