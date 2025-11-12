<?php
// Clase DetalleFactura hereda de la clase Conectar
class DetalleFactura extends Conectar {

    // Obtiene todos los detalles de facturas con información completa
    public function obtener_detalles() {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Consulta SQL para obtener todos los detalles con información de productos y facturas
        $consulta_sql = "SELECT df.id_detalle, df.id_factura, df.codigo_producto, df.precio_producto, 
                               df.cantidad_producto, df.subtotal, p.nombre as producto_nombre,
                               ef.fecha as fecha_factura, c.nombre as cliente_nombre
                        FROM detalle_factura df 
                        LEFT JOIN productos p ON df.codigo_producto = p.codigo
                        LEFT JOIN encabezado_factura ef ON df.id_factura = ef.id
                        LEFT JOIN cliente c ON ef.cedula_cliente = c.cedula
                        ORDER BY df.id_factura DESC, df.id_detalle";   

        // Prepara la consulta SQL
        $consulta = $conexion->prepare($consulta_sql);
        $consulta->execute();

        // Retorna el resultado de la consulta como un array asociativo
        return $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);   
    }

    // Obtiene detalles de una factura específica
    public function obtener_detalles_por_factura($id_factura) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Consulta SQL para obtener detalles de una factura específica
        $consulta_sql = "SELECT df.id_detalle, df.id_factura, df.codigo_producto, df.precio_producto, 
                               df.cantidad_producto, df.subtotal, p.nombre as producto_nombre
                        FROM detalle_factura df 
                        LEFT JOIN productos p ON df.codigo_producto = p.codigo
                        WHERE df.id_factura = ?
                        ORDER BY df.id_detalle";

        // Prepara la consulta SQL
        $consulta = $conexion->prepare($consulta_sql);
        $consulta->bindValue(1, $id_factura);  // Asocia el valor del ID de factura

        // Ejecuta la consulta
        $consulta->execute();

        // Retorna el resultado como un array asociativo
        return $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene un detalle específico por su ID
    public function obtener_detalle_por_id($id_detalle) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Consulta SQL para obtener un detalle específico por su ID
        $consulta_sql = "SELECT df.id_detalle, df.id_factura, df.codigo_producto, df.precio_producto, 
                               df.cantidad_producto, df.subtotal, p.nombre as producto_nombre
                        FROM detalle_factura df 
                        LEFT JOIN productos p ON df.codigo_producto = p.codigo
                        WHERE df.id_detalle = ?";

        // Prepara la consulta SQL
        $consulta = $conexion->prepare($consulta_sql);
        $consulta->bindValue(1, $id_detalle);  // Asocia el valor del ID del detalle

        // Ejecuta la consulta
        $consulta->execute();

        // Retorna el resultado como un array asociativo
        return $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Inserta un nuevo detalle de factura
    public function insertar_detalle($id_factura, $codigo_producto, $precio_producto, $cantidad_producto) {
        try {
            // Establece la conexión a la base de datos
            $conexion = parent::conectar_bd();
            parent::establecer_codificacion();
            
            // Calcular el subtotal
            $subtotal = $precio_producto * $cantidad_producto;
            
            // Sentencia SQL para insertar un nuevo detalle
            $sentencia_sql = "INSERT INTO detalle_factura(id_detalle, id_factura, codigo_producto, precio_producto, cantidad_producto, subtotal) 
                             VALUES (NULL, ?, ?, ?, ?, ?)";

            // Prepara la sentencia SQL
            $sentencia = $conexion->prepare($sentencia_sql);
            $sentencia->bindValue(1, $id_factura);  // Asocia el ID de la factura
            $sentencia->bindValue(2, $codigo_producto);  // Asocia el código del producto
            $sentencia->bindValue(3, $precio_producto);  // Asocia el precio del producto
            $sentencia->bindValue(4, $cantidad_producto);  // Asocia la cantidad del producto
            $sentencia->bindValue(5, $subtotal);  // Asocia el subtotal calculado

            // Ejecuta la sentencia
            $sentencia->execute();

            // Retorna el ID del detalle insertado
            return $conexion->lastInsertId();
            
        } catch (Exception $e) {
            error_log("Error en insertar_detalle: " . $e->getMessage());
            return false;
        }
    }

    // Actualiza un detalle de factura existente
    public function actualizar_detalle($id_detalle, $id_factura, $codigo_producto, $precio_producto, $cantidad_producto) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Calcular el nuevo subtotal
        $subtotal = $precio_producto * $cantidad_producto;
        
        // Sentencia SQL para actualizar un detalle existente
        $sentencia_sql = "UPDATE detalle_factura SET id_factura = ?, codigo_producto = ?, precio_producto = ?, 
                         cantidad_producto = ?, subtotal = ? WHERE id_detalle = ?";

        // Prepara la sentencia SQL
        $sentencia = $conexion->prepare($sentencia_sql);
        $sentencia->bindValue(1, $id_factura);  // Asocia el ID de la factura
        $sentencia->bindValue(2, $codigo_producto);  // Asocia el código del producto
        $sentencia->bindValue(3, $precio_producto);  // Asocia el precio del producto
        $sentencia->bindValue(4, $cantidad_producto);  // Asocia la cantidad del producto
        $sentencia->bindValue(5, $subtotal);  // Asocia el subtotal calculado
        $sentencia->bindValue(6, $id_detalle);   // Asocia el ID del detalle a actualizar

        // Ejecuta la sentencia
        $sentencia->execute();

        // Retorna el resultado
        return $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    // Elimina un detalle de factura
    public function eliminar_detalle($id_detalle) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Sentencia SQL para eliminar un detalle
        $sentencia_sql = "DELETE FROM detalle_factura WHERE id_detalle = ?";

        // Prepara la sentencia SQL
        $sentencia = $conexion->prepare($sentencia_sql);
        $sentencia->bindValue(1, $id_detalle);  // Asocia el ID del detalle

        // Ejecuta la sentencia
        $sentencia->execute();

        // Retorna el resultado
        return $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    // Calcula el total de una factura basado en sus detalles
    public function calcular_total_factura($id_factura) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Consulta SQL para sumar todos los subtotales de una factura
        $consulta_sql = "SELECT SUM(subtotal) as total FROM detalle_factura WHERE id_factura = ?";

        // Prepara la consulta SQL
        $consulta = $conexion->prepare($consulta_sql);
        $consulta->bindValue(1, $id_factura);

        // Ejecuta la consulta
        $consulta->execute();

        // Obtiene el resultado
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        
        // Retorna el total (0 si no hay detalles)
        return $resultado['total'] ? $resultado['total'] : 0;
    }

    // Obtiene productos más vendidos (reporte)
    public function obtener_productos_mas_vendidos($limite = 10) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Consulta SQL para obtener productos más vendidos
        $consulta_sql = "SELECT df.codigo_producto, p.nombre as producto_nombre,
                               SUM(df.cantidad_producto) as total_vendido,
                               SUM(df.subtotal) as ingresos_totales
                        FROM detalle_factura df 
                        LEFT JOIN productos p ON df.codigo_producto = p.codigo
                        GROUP BY df.codigo_producto, p.nombre
                        ORDER BY total_vendido DESC
                        LIMIT ?";

        // Prepara la consulta SQL
        $consulta = $conexion->prepare($consulta_sql);
        $consulta->bindValue(1, $limite, PDO::PARAM_INT);

        // Ejecuta la consulta
        $consulta->execute();

        // Retorna el resultado como un array asociativo
        return $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>