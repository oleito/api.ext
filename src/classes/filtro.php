<?php

class filtro
{
    //datos para la mysqli con servidor MySQL.
    //Mover a otra clase.
    public function __construct()
    {
    }

    /**
     * Conecta a la base de datos
     *
     * @return true si la conexion es exitosa
     */
    public function stringFilter($stringIn)
    {
        // Produce: Hll Wrld f PHP
        $vowels = array("=", "==", "===", "'", '"', "or", "and", "while", "DROP", "drop", ";", ",");
        $stringOut = str_ireplace($vowels, "", $stringIn);
        $stringOut = addslashes($stringOut);
        return $stringOut;
    }

}
