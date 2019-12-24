<?php

class mysql
{
    //datos para la mysqli con servidor MySQL.
    //Mover a otra clase.
    private $DBhostName = 'parisautos.com.ar';
    private $DBuserName = 'parisaut_orionusr';
    private $DBuserPassword = 'Be1sIByM7HR@';
    private $DBdataBaseName = 'parisaut_chapa';

    public $mysqli;

    public function __construct()
    {
    }

    /**
     * Conecta a la base de datos
     *
     * @return true si la conexion es exitosa
     */
    public function conectar()
    {
        try {
            @$this->mysqli = new mysqli(
                $this->DBhostName,
                $this->DBuserName,
                $this->DBuserPassword,
                $this->DBdataBaseName
            ) or die(mysql_error());
            @$this->mysqli->set_charset("utf8");

            if ($this->mysqli->connect_errno) {
                return false;
            }
            return true;

        } catch (\Throwable $th) {
            return false;
        }
    }

    public function conectarPDO()
    {
        try {
            $conexion_mysql = "mysql:host=$this->DBhostName;dbname=$this->DBdataBaseName";
            $conexionDB = new PDO($conexion_mysql, $this->DBuserName, $this->DBuserPassword);
            $conexionDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //condificacion de caracteres
            //$conexionDB -> exec("set name utf8");
            return $conexionDB;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Inserta un valor en una base de datos
     *
     * @param [text] $tabla
     * @param [array] $datos
     * @return true si inserta correctamente
     */
    public function insertar($tabla, $datos) #"usuario", "null, 'usr@dom'"

    {
        if ($this->mysqli->query("INSERT INTO parisaut_chapa." . $tabla . " VALUES ($datos)")) {
            return true;
        } else {
            echo "INSERT INTO parisaut_chapa." . $tabla . " VALUES ($datos)";
            return false;
        }
    }

    /**
     * Busca registros segun el criterio dado
     *
     * @param [text] $tabla
     * @param [text] $condicion
     * @return array resultados de la busqueda
     */
    public function buscar($tabla, $condicion) #'usuario', 'idusuario > 0'

    {
        $resultado = $this->mysqli->query("SELECT * FROM parisaut_chapa." . $tabla . " WHERE $condicion LIMIT 1");
        //echo ("SELECT * FROM $tabla WHERE $condicion LIMIT 1");
        if ($resultado) {
            return $resultado->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    /**
     * Busca registros segun el criterio dado
     *
     * @param [text] $tabla
     * @param [text] $condicion
     * @return array resultados de la busqueda
     */
    public function buscarUnitario($tabla, $condicion) #'usuario', 'idusuario > 0'

    {
        $resultado = $this->mysqli->query("SELECT * FROM $tabla WHERE $condicion LIMIT 1");
        if ($resultado) {
            return $resultado->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    /**
     * Lista la base de datos completa
     *
     * @param [type] $tabla
     * @return array todos los registros
     */
    public function listar($tabla) //"usuarios","1"

    {
        $resultado = $this->mysqli->query("SELECT * FROM $tabla");
        if ($resultado) {
            return $resultado->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    /**
     * Actualiza un registro
     *
     * @param [string] $tabla
     * @param [string] $campos
     * @param [string] $condicion
     * @return true si logro actualizar
     */
    public function actualizar($tabla, $campos, $condicion) #"usuario", " usuario_username = 'j.q@p.com.ar', ","idusuario = 4"

    {
        $resultado = $this->mysqli->query("UPDATE $tabla SET $campos WHERE $condicion");
        //echo ("UPDATE $tabla SET $campos WHERE $condicion");
        if ($resultado) {
            return true;
        }
        return false;
    }

    /**
     * Borra uno o mas registros segun la condicion.
     *
     * @param [string] $tabla
     * @param [string] $condicion
     * @return void
     */
    public function borrar($tabla, $condicion) #'usuario', 'idusuario = 3'

    {
        $resultado = $this->mysqli->query("DELETE FROM $tabla WHERE $condicion") or die($this->mysqli->error);
        if ($resultado) {
            return true;
        }
        return false;
    }

}
