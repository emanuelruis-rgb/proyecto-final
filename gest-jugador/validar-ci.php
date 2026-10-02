<?php
function validarCI($ci) {
    $ci = str_replace(['.','-',' '], '', $ci);
    if(!ctype_digit($ci) || strlen($ci)<7 || strlen($ci)>8){
        return false;
    }
    if(preg_match('/^(\d)\1+$/', $ci)){
        return false;
    }
    $ci = str_pad($ci, 8, '0', STR_PAD_LEFT);
    $pesos = [2,9,8,7,6,3,4];
    $suma = 0;
    for($i =0;$i<7;$i++){
        $suma += $ci[$i] * $pesos[$i];
    }
    return (10-$suma%10) % 10 == $ci[7];
}
?>