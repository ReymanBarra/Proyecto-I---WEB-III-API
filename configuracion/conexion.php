<?php
// Clase Conectar para manejar la conexión a la base de datos
class Conectar {
    // Variable protegida para almacenar la instancia de la conexión
    protected $conexion_bd;
    
    // Método protegido para establecer la conexión con la base de datos
    protected function conectar_bd() {
        try {
            // Establece la conexión utilizando PDO
            $conexion = $this->conexion_bd = new PDO("mysql:host=localhost;dbname=verduleria", "root", "1234");
            return $conexion;
        } catch (Exception $e) {
            // Si ocurre un error, muestra el mensaje de error y detiene la ejecución
            print "Error en la base de datos: " . $e->getMessage() . "<br/>";
            die();  // Detiene la ejecución
        }
    }

    // Método público para establecer la codificación de caracteres a UTF-8
    public function establecer_codificacion() {
        // Ejecuta la sentencia SQL para configurar la codificación de caracteres a UTF-8
        return $this->conexion_bd->query("SET NAMES 'utf8'");
    }
}
?>