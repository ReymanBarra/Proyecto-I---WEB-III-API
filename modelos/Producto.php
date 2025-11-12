<?php
// Clase Producto hereda de la clase Conectar
class Producto extends Conectar {

    // Obtiene todos los productos
    public function obtener_productos() {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Consulta SQL para obtener todos los productos con información de categoría
        $consulta_sql = "SELECT p.codigo, p.nombre, p.precio, p.id_categoria, c.nombre as categoria_nombre 
                        FROM productos p 
                        LEFT JOIN categorias c ON p.id_categoria = c.id";   

        // Prepara la consulta SQL
        $consulta = $conexion->prepare($consulta_sql);
        $consulta->execute();

        // Retorna el resultado de la consulta como un array asociativo
        return $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);   
    }

    // Obtiene un producto específico por su código
    public function obtener_producto_por_codigo($codigo_producto) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Consulta SQL para obtener un producto específico por su código
        $consulta_sql = "SELECT p.codigo, p.nombre, p.precio, p.id_categoria, c.nombre as categoria_nombre 
                        FROM productos p 
                        LEFT JOIN categorias c ON p.id_categoria = c.id 
                        WHERE p.codigo = ?";

        // Prepara la consulta SQL
        $consulta = $conexion->prepare($consulta_sql);
        $consulta->bindValue(1, $codigo_producto);  // Asocia el valor del código de producto

        // Ejecuta la consulta
        $consulta->execute();

        // Retorna el resultado como un array asociativo
        return $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene productos por categoría
    public function obtener_productos_por_categoria($id_categoria) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Consulta SQL para obtener productos por categoría
        $consulta_sql = "SELECT p.codigo, p.nombre, p.precio, p.id_categoria, c.nombre as categoria_nombre 
                        FROM productos p 
                        LEFT JOIN categorias c ON p.id_categoria = c.id 
                        WHERE p.id_categoria = ?";

        // Prepara la consulta SQL
        $consulta = $conexion->prepare($consulta_sql);
        $consulta->bindValue(1, $id_categoria);

        // Ejecuta la consulta
        $consulta->execute();

        // Retorna el resultado como un array asociativo
        return $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Inserta un nuevo producto
    public function insertar_producto($codigo, $nombre, $precio, $id_categoria) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Sentencia SQL para insertar un nuevo producto
        $sentencia_sql = "INSERT INTO productos(codigo, nombre, precio, id_categoria) VALUES (?, ?, ?, ?)";

        // Prepara la sentencia SQL
        $sentencia = $conexion->prepare($sentencia_sql);
        $sentencia->bindValue(1, $codigo);  // Asocia el código del producto
        $sentencia->bindValue(2, $nombre);  // Asocia el nombre del producto
        $sentencia->bindValue(3, $precio);  // Asocia el precio del producto
        $sentencia->bindValue(4, $id_categoria);  // Asocia el ID de categoría

        // Ejecuta la sentencia
        $sentencia->execute();

        // Retorna el resultado
        return $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualiza un producto existente
    public function actualizar_producto($codigo, $nombre, $precio, $id_categoria) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Sentencia SQL para actualizar un producto existente
        $sentencia_sql = "UPDATE productos SET nombre = ?, precio = ?, id_categoria = ? WHERE codigo = ?";

        // Prepara la sentencia SQL
        $sentencia = $conexion->prepare($sentencia_sql);
        $sentencia->bindValue(1, $nombre);  // Asocia el nombre del producto
        $sentencia->bindValue(2, $precio);  // Asocia el precio del producto
        $sentencia->bindValue(3, $id_categoria);  // Asocia el ID de categoría
        $sentencia->bindValue(4, $codigo);   // Asocia el código del producto a actualizar

        // Ejecuta la sentencia
        $sentencia->execute();

        // Retorna el resultado
        return $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    // Elimina un producto
    public function eliminar_producto($codigo_producto) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Sentencia SQL para eliminar un producto
        $sentencia_sql = "DELETE FROM productos WHERE codigo = ?";

        // Prepara la sentencia SQL
        $sentencia = $conexion->prepare($sentencia_sql);
        $sentencia->bindValue(1, $codigo_producto);  // Asocia el código del producto

        // Ejecuta la sentencia
        $sentencia->execute();

        // Retorna el resultado
        return $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>