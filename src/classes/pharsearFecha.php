
<?php
/*
 */
function parsearFecha($fecha)
{
    //"27/9/2018"
    //
    $f = explode("/", $fecha);
    $d = $f[0]; //dia
    $m = $f[1]; //mes
    $a = $f[2]; //año
    return $a . "-" . $m . "-" . $d;
};