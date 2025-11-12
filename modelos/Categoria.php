<?php
// Clase Categoria hereda de la clase Conectar
class Categoria extends Conectar {

    // Obtiene todas las categorías
    public function obtener_categorias() {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Consulta SQL para obtener todas las categorías
        $consulta_sql = "SELECT * FROM categorias";   

        // Prepara la consulta SQL
        $consulta = $conexion->prepare($consulta_sql);
        $consulta->execute();

        // Retorna el resultado de la consulía como un array asociativo
        return $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);   
    }

    // Obtiene una categoría específica por su ID
    public function obtener_categoria_por_id($id_categoria) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Consulta SQL para obtener una categoría específica por su ID
        $consulta_sql = "SELECT * FROM categorias WHERE id = ?";

        // Prepara la consulta SQL
        $consulta = $conexion->prepare($consulta_sql);
        $consulta->bindValue(1, $id_categoria);  // Asocia el valor del ID de categoría

        // Ejecuta la consulta
        $consulta->execute();

        // Retorna el resultado como un array asociativo
        return $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Inserta una nueva categoría
    public function insertar_categoria($nombre_categoria) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Sentencia SQL para insertar una nueva categoría
        $sentencia_sql = "INSERT INTO categorias(id, nombre) VALUES (NULL, ?)";

        // Prepara la sentencia SQL
        $sentencia = $conexion->prepare($sentencia_sql);
        $sentencia->bindValue(1, $nombre_categoria);  // Asocia el nombre de la categoría

        // Ejecuta la sentencia
        $sentencia->execute();

        // Retorna el resultado (aunque no es necesario para un insert, se puede omitir)
        return $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualiza una categoría existente
    public function actualizar_categoria($id_categoria, $nombre_categoria) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Sentencia SQL para actualizar una categoría existente
        $sentencia_sql = "UPDATE categorias SET nombre = ? WHERE id = ?";

        // Prepara la sentencia SQL
        $sentencia = $conexion->prepare($sentencia_sql);
        $sentencia->bindValue(1, $nombre_categoria);  // Asocia el nombre de la categoría
        $sentencia->bindValue(2, $id_categoria);   // Asocia el ID de la categoría a actualizar

        // Ejecuta la sentencia
        $sentencia->execute();

        // Retorna el resultado (aunque no es necesario para un update, se puede omitir)
        return $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    // Elimina una categoría
    public function eliminar_categoria($id_categoria) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Sentencia SQL para eliminar una categoría
        $sentencia_sql = "DELETE FROM categorias WHERE id = ?";

        // Prepara la sentencia SQL
        $sentencia = $conexion->prepare($sentencia_sql);
        $sentencia->bindValue(1, $id_categoria);  // Asocia el ID de la categoría

        // Ejecuta la sentencia
        $sentencia->execute();

        // Retorna el resultado (aunque no es necesario para un delete, se puede omitir)
        return $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    // Verifica si un usuario existe en la tabla usuarios
    public function verificar_key_usuario($key) {
        try {
            // Establece la conexión a la base de datos
            $conexion = parent::conectar_bd();
            parent::establecer_codificacion();
            
            // Debug: Log del KEY que se está buscando
            error_log("Buscando usuario en BD: " . $key);
            
            // Consulta SQL para verificar si el usuario existe en la tabla usuarios
            $consulta_sql = "SELECT COUNT(*) as total FROM usuarios WHERE nombre = ?";

            // Prepara la consulta SQL
            $consulta = $conexion->prepare($consulta_sql);
            $consulta->bindValue(1, $key);  // Asocia el valor del usuario

            // Ejecuta la consulta
            $consulta->execute();

            // Obtiene el resultado
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
            
            // Debug: Log del resultado
            error_log("Resultado de la consulta: " . json_encode($resultado));
            
            // Retorna true si el usuario existe (total > 0), false si no existe
            return $resultado['total'] > 0;
            
        } catch (Exception $e) {
            error_log("Error en verificar_key_usuario: " . $e->getMessage());
            return false;
        }
    }

    // Obtiene todos los datos de un usuario por su usuario
    public function obtener_usuario_por_key($key) {
        try {
            // Establece la conexión a la base de datos
            $conexion = parent::conectar_bd();
            parent::establecer_codificacion();
            
            // Consulta SQL para obtener todos los datos del usuario por usuario
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
