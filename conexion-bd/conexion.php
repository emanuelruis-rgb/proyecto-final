<?php

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