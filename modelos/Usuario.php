<?php


require_once(__DIR__ . "/../configuracion/conexion.php");


class Usuario
{
    public function __construct()
    {
    }

    // INSERTAR
    public function insertar($cedula, $nombre, $edad, $telefono)
    {
        try {
            // Verificar si la cédula ya existe
            $sql_check = "SELECT cedula FROM usuarios WHERE cedula = '$cedula'";
            $res_check = ejecutarConsulta($sql_check);

            $row = $res_check->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                return 1062; // Duplicado
            } else {
                $sql = "INSERT INTO usuarios (cedula, nombre, edad, telefono)
                        VALUES ('$cedula', '$nombre', '$edad', '$telefono')";
                $insert = ejecutarConsulta($sql);

                return $insert ? 1 : 0;
            }

        } catch (Exception $e) {
            return $e->getCode();
        }
    }

    // EDITAR
    public function editar($cedula, $nombre, $edad, $telefono)
    {
        $sql = "UPDATE usuarios 
                SET nombre='$nombre', edad='$edad', telefono='$telefono' 
                WHERE cedula='$cedula'";

        return ejecutarConsulta($sql);
    }

    // ELIMINAR
    public function eliminar($cedula)
    {
        $sql = "DELETE FROM usuarios WHERE cedula='$cedula'";
        return ejecutarConsulta($sql);
    }

    // MOSTRAR 1 registro
    public function mostrar($cedula)
    {
        $sql = "SELECT * FROM usuarios WHERE cedula='$cedula'";
        return ejecutarConsultaSimpleFila($sql);
    }

    // LISTAR todos
    public function listar()
    {
        $sql = "SELECT * FROM usuarios";
        return ejecutarConsulta($sql);
    }
}
?>
