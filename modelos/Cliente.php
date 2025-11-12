<?php
// Clase Cliente hereda de la clase Conectar
class Cliente extends Conectar {

    // Obtiene todos los clientes
    public function obtener_clientes() {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Consulta SQL para obtener todos los clientes
        $consulta_sql = "SELECT * FROM cliente";   

        // Prepara la consulta SQL
        $consulta = $conexion->prepare($consulta_sql);
        $consulta->execute();

        // Retorna el resultado de la consulta como un array asociativo
        return $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);   
    }

    // Obtiene un cliente específico por su cédula
    public function obtener_cliente_por_cedula($cedula_cliente) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Consulta SQL para obtener un cliente específico por su cédula
        $consulta_sql = "SELECT * FROM cliente WHERE cedula = ?";

        // Prepara la consulta SQL
        $consulta = $conexion->prepare($consulta_sql);
        $consulta->bindValue(1, $cedula_cliente);  // Asocia el valor de la cédula del cliente

        // Ejecuta la consulta
        $consulta->execute();

        // Retorna el resultado como un array asociativo
        return $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Inserta un nuevo cliente
    public function insertar_cliente($cedula, $nombre, $telefono = null) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Sentencia SQL para insertar un nuevo cliente
        $sentencia_sql = "INSERT INTO cliente(cedula, nombre, telefono) VALUES (?, ?, ?)";

        // Prepara la sentencia SQL
        $sentencia = $conexion->prepare($sentencia_sql);
        $sentencia->bindValue(1, $cedula);  // Asocia la cédula del cliente
        $sentencia->bindValue(2, $nombre);  // Asocia el nombre del cliente
        $sentencia->bindValue(3, $telefono);  // Asocia el teléfono del cliente

        // Ejecuta la sentencia
        $sentencia->execute();

        // Retorna el resultado
        return $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualiza un cliente existente
    public function actualizar_cliente($cedula, $nombre, $telefono = null) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Sentencia SQL para actualizar un cliente existente
        $sentencia_sql = "UPDATE cliente SET nombre = ?, telefono = ? WHERE cedula = ?";

        // Prepara la sentencia SQL
        $sentencia = $conexion->prepare($sentencia_sql);
        $sentencia->bindValue(1, $nombre);  // Asocia el nombre del cliente
        $sentencia->bindValue(2, $telefono);  // Asocia el teléfono del cliente
        $sentencia->bindValue(3, $cedula);   // Asocia la cédula del cliente a actualizar

        // Ejecuta la sentencia
        $sentencia->execute();

        // Retorna el resultado
        return $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    // Elimina un cliente
    public function eliminar_cliente($cedula_cliente) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Sentencia SQL para eliminar un cliente
        $sentencia_sql = "DELETE FROM cliente WHERE cedula = ?";

        // Prepara la sentencia SQL
        $sentencia = $conexion->prepare($sentencia_sql);
        $sentencia->bindValue(1, $cedula_cliente);  // Asocia la cédula del cliente

        // Ejecuta la sentencia
        $sentencia->execute();

        // Retorna el resultado
        return $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    // Busca clientes por nombre (búsqueda parcial)
    public function buscar_clientes_por_nombre($nombre) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Consulta SQL para buscar clientes por nombre
        $consulta_sql = "SELECT * FROM cliente WHERE nombre LIKE ?";

        // Prepara la consulta SQL
        $consulta = $conexion->prepare($consulta_sql);
        $consulta->bindValue(1, '%' . $nombre . '%');  // Búsqueda parcial

        // Ejecuta la consulta
        $consulta->execute();

        // Retorna el resultado como un array asociativo
        return $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>