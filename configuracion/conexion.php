<?php

class Conectar
{
    private $conexion_bd;

    public function conectar_bd()
    {
        try {
            // Datos internos de Railway
            $host = "mysql.railway.internal";
            $port = "3306";
            $dbname = "railway";
            $user = "root";
            $pass = "xFDGacnilabXUBmAhpwRsfTUT0aYVAsi";

            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";

            $this->conexion_bd = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

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
