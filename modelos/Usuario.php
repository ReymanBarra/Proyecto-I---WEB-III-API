<?php
// Clase Usuario hereda de la clase Conectar
class Usuario extends Conectar {

    // Obtiene todos los usuarios
    public function obtener_usuarios() {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Consulta SQL para obtener todos los usuarios
        $consulta_sql = "SELECT * FROM usuarios";   

        // Prepara la consulta SQL
        $consulta = $conexion->prepare($consulta_sql);
        $consulta->execute();

        // Retorna el resultado de la consulta como un array asociativo
        return $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);   
    }

    // Obtiene un usuario específico por su cédula
    public function obtener_usuario_por_cedula($cedula_usuario) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Consulta SQL para obtener un usuario específico por su cédula
        $consulta_sql = "SELECT * FROM usuarios WHERE cedula = ?";

        // Prepara la consulta SQL
        $consulta = $conexion->prepare($consulta_sql);
        $consulta->bindValue(1, $cedula_usuario);  // Asocia el valor de la cédula de usuario

        // Ejecuta la consulta
        $consulta->execute();

        // Retorna el resultado como un array asociativo
        return $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Inserta un nuevo usuario
    public function insertar_usuario($cedula, $nombre, $llave) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Sentencia SQL para insertar un nuevo usuario
        $sentencia_sql = "INSERT INTO usuarios(cedula, nombre, llave) VALUES (?, ?, ?)";

        // Prepara la sentencia SQL
        $sentencia = $conexion->prepare($sentencia_sql);
        $sentencia->bindValue(1, $cedula);  // Asocia la cédula
        $sentencia->bindValue(2, $nombre);  // Asocia el nombre
        $sentencia->bindValue(3, $llave);  // Asocia la llave

        // Ejecuta la sentencia
        $sentencia->execute();

        // Retorna el resultado
        return $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualiza un usuario existente
    public function actualizar_usuario($cedula, $nombre, $llave) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Sentencia SQL para actualizar un usuario existente
        $sentencia_sql = "UPDATE usuarios SET nombre = ?, llave = ? WHERE cedula = ?";

        // Prepara la sentencia SQL
        $sentencia = $conexion->prepare($sentencia_sql);
        $sentencia->bindValue(1, $nombre);  // Asocia el nombre
        $sentencia->bindValue(2, $llave);  // Asocia la llave
        $sentencia->bindValue(3, $cedula);   // Asocia la cédula del usuario a actualizar

        // Ejecuta la sentencia
        $sentencia->execute();

        // Retorna el resultado
        return $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    // Elimina un usuario
    public function eliminar_usuario($cedula_usuario) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Sentencia SQL para eliminar un usuario
        $sentencia_sql = "DELETE FROM usuarios WHERE cedula = ?";

        // Prepara la sentencia SQL
        $sentencia = $conexion->prepare($sentencia_sql);
        $sentencia->bindValue(1, $cedula_usuario);  // Asocia la cédula del usuario

        // Ejecuta la sentencia
        $sentencia->execute();

        // Retorna el resultado
        return $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    // Verifica las credenciales de un usuario para login
    public function verificar_usuario($cedula, $nombre) {
        try {
            // Establece la conexión a la base de datos
            $conexion = parent::conectar_bd();
            parent::establecer_codificacion();
            
            // Consulta SQL para obtener el usuario
            $consulta_sql = "SELECT cedula, nombre, llave FROM usuarios WHERE cedula = ? AND nombre = ?";

            // Prepara la consulta SQL
            $consulta = $conexion->prepare($consulta_sql);
            $consulta->bindValue(1, $cedula);
            $consulta->bindValue(2, $nombre);

            // Ejecuta la consulta
            $consulta->execute();
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

            // Verifica si el usuario existe
            if ($resultado) {
                return $resultado;
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Error en verificar_usuario: " . $e->getMessage());
            return false;
        }
    }

    // Obtiene la llave de encriptación por cédula del programador
    public function obtener_llave_por_cedula($cedula) {
        try {
            // Establece la conexión a la base de datos
            $conexion = parent::conectar_bd();
            parent::establecer_codificacion();
            
            // Consulta SQL para obtener la llave de encriptación
            $consulta_sql = "SELECT llave FROM usuarios WHERE cedula = ?";

            // Prepara la consulta SQL
            $consulta = $conexion->prepare($consulta_sql);
            $consulta->bindValue(1, $cedula);

            // Ejecuta la consulta
            $consulta->execute();
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

            // Retorna la llave si existe
            return $resultado ? $resultado['llave'] : false;
            
        } catch (Exception $e) {
            error_log("Error en obtener_llave_por_cedula: " . $e->getMessage());
            return false;
        }
    }

    // Verifica si un usuario existe en la tabla usuarios (para compatibilidad con categoria.php)
    public function verificar_key_usuario($key) {
        try {
            // Establece la conexión a la base de datos
            $conexion = parent::conectar_bd();
            parent::establecer_codificacion();
            
            // Consulta SQL para verificar si el usuario existe en la tabla usuarios
            $consulta_sql = "SELECT COUNT(*) as total FROM usuarios WHERE nombre = ?";

            // Prepara la consulta SQL
            $consulta = $conexion->prepare($consulta_sql);
            $consulta->bindValue(1, $key);  // Asocia el valor del usuario

            // Ejecuta la consulta
            $consulta->execute();

            // Obtiene el resultado
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
            
            // Retorna true si el usuario existe (total > 0), false si no existe
            return $resultado['total'] > 0;
            
        } catch (Exception $e) {
            error_log("Error en verificar_key_usuario: " . $e->getMessage());
            return false;
        }
    }

    // Obtiene todos los datos de un usuario por su nombre (para compatibilidad con categoria.php)
    public function obtener_usuario_por_key($key) {
        try {
            // Establece la conexión a la base de datos
            $conexion = parent::conectar_bd();
            parent::establecer_codificacion();
            
            // Consulta SQL para obtener todos los datos del usuario por nombre
            $consulta_sql = "SELECT cedula, nombre, llave FROM usuarios WHERE nombre = ?";

            // Prepara la consulta SQL
            $consulta = $conexion->prepare($consulta_sql);
            $consulta->bindValue(1, $key);  // Asocia el valor del usuario

            // Ejecuta la consulta
            $consulta->execute();

            // Retorna el resultado como un array asociativo
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error en obtener_usuario_por_key: " . $e->getMessage());
            return [];
        }
    }

}
?>