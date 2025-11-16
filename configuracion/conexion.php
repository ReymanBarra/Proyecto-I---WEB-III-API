<?php

class Conectar
{
    private $conexion_bd;

    public function conectar_bd()
    {
        try {
            $this->conexion_bd = new PDO(
                "mysql:host=mysql.railway.internal;port=3306;dbname=railway",
                "root",
                "xFDGacnilabXUBmAhpwRsfTUT0aYVAsi"
            );
            return $this->conexion_bd;
        } catch (Exception $e) {
            print "Error en la base de datos: " . $e->getMessage();
            die();
        }
    }

    public function establecer_codificacion()
    {
        return $this->conexion_bd->query("SET NAMES 'utf8'");
    }
}

function ejecutarConsulta($sql)
{
    $db = new Conectar();
    $pdo = $db->conectar_bd();
    $db->establecer_codificacion();
    return $pdo->query($sql);
}

function ejecutarConsultaSimpleFila($sql)
{
    $db = new Conectar();
    $pdo = $db->conectar_bd();
    $db->establecer_codificacion();

    $query = $pdo->query($sql);
    return $query->fetch(PDO::FETCH_OBJ);
}

function ejecutarConsulta_retornarID($sql)
{
    $db = new Conectar();
    $pdo = $db->conectar_bd();
    $db->establecer_codificacion();

    $pdo->query($sql);
    return $pdo->lastInsertId();
}

?>
