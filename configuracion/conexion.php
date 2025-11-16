<?php

class Conectar
{
    private $conexion_bd;

    public function conectar_bd()
    {
        try {
            $this->conexion_bd = new PDO(
                "mysql:host=shuttle.proxy.rlwy.net;port=46221;dbname=railway",
                "root",
                "xFDGacnilabXUBmAhpwRsfTUTOaYVAsi"
            );

            // Habilitar errores
            $this->conexion_bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
