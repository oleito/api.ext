<?php

class mysql
{
    //datos para la conexion con servidor MySQL.
    //Mover a otra clase.
    private $hostName = 'parisautos.com.ar';
    private $userName = 'parisaut_orionusr';
    private $userPassword = 'Be1sIByM7HR@';
    private $dataBaseName = 'parisaut_chapa';

    public $conexion;

    public function __construct()
    {
    }

    public function conectar()
    {
        try {
            @$this->conexion = new mysqli(
                $this->hostName,
                $this->userName,
                $this->userPassword,
                $this->dataBaseName
            ) or die(mysql_error());
            @$this->conexion->set_charset("utf8");

            if ($this->conexion->connect_errno) {
                return false;
            }
            return true;

        } catch (\Throwable $th) {
            return false;
        }

    }

    public function insertar($tabla, $datos)//"usuarios","'PAPA','JUAN PABLO','foto.jpg'"
    {
        if ($this->conexion->query("INSERT INTO $tabla VALUES ($datos)")) {
            return true;
        } else {
            return false;
        }
    }

    public function buscar($tabla, $condicion) //"usuarios","1"
    {
        $resultado = $this->conexion->query("SELECT * FROM $tabla WHERE $condicion");
        if ($resultado) {
            return $resultado->fetch_all(mysqli_assoc);
        }
    }

    public function buscarUnitario($tabla, $condicion) //"usuarios","1"
    {
        $resultado = $this->conexion->query("SELECT * FROM $tabla WHERE $condicion  LIMIT 1");
        if ($resultado) {
            return $resultado->fetch_all(mysqli_assoc);
        }
    }

    public function actualizar($tabla, $campos, $condicion)//"usuarios","nombre='ANAMARIA'","id=1"
    {
        $resultado = $this->conexion->query("UPDATE $tabla SET $campos WHERE $condicion");
        if ($resultado) {
            return true;
        }
        return false;
    }

    public function borrar($tabla, $condicion)//"usuarios","id=1"
    {
        $resultado = $this->conexion->query("DELETE FROM $tabla WHERE $condicion") or die($this->conexion->error);
        if ($resultado) {
            return true;
        }
        return false;
    }
}
