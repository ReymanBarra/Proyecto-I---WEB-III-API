<?php
require_once(__DIR__ . "/../configuracion/conexion.php");


class Api
{
    public function __construct()
    {
    }

    // Verifica si la cédula existe y está activa (Status = 1)
    public function VerificarKEY($cedula)
    {
        $sql = "SELECT * FROM api WHERE Cedula = '$cedula' AND Status = 1";
        $query = ejecutarConsulta($sql);

        $resultado = [];

        // Guardar resultados en un array
        while ($fila = $query->fetch(PDO::FETCH_ASSOC)) {
            $resultado[] = $fila;
        }

        return $resultado; // Si viene vacío: credenciales no válidas
    }

    // Verifica si la cédula existe pero está DESACTIVADA
    public function VerificarDesactivado($cedula)
    {
        $sql = "SELECT * FROM api WHERE Cedula = '$cedula' AND Status = 0";
        $query = ejecutarConsulta($sql);
        return $query->fetch(PDO::FETCH_ASSOC);  
    }
}
?>
