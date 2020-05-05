<?php

class mensajesPendientes
{
    public function __construct()
    {

    }

    public function getMensajesPendientes()
    {
        $db = new db();
        $db = $db->conectar();
        $consulta = "SELECT * FROM mensaje";
        $ejecutar = $db->query($consulta);

        $mensaje = $ejecutar->fetchAll(PDO::FETCH_ASSOC);

        return count($mensaje);
    }
}
