<?php
// Clase EncabezadoFactura hereda de la clase Conectar
class EncabezadoFactura extends Conectar {

    // Obtiene todas las facturas con información del cliente
    public function obtener_facturas() {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Consulta SQL para obtener todas las facturas con información del cliente
        $consulta_sql = "SELECT ef.id, ef.cedula_cliente, ef.fecha, ef.total, c.nombre as cliente_nombre 
                        FROM encabezado_factura ef 
                        LEFT JOIN cliente c ON ef.cedula_cliente = c.cedula 
                        ORDER BY ef.fecha DESC";   

        // Prepara la consulta SQL
        $consulta = $conexion->prepare($consulta_sql);
        $consulta->execute();

        // Retorna el resultado de la consulta como un array asociativo
        return $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);   
    }

    // Obtiene una factura específica por su ID
    public function obtener_factura_por_id($id_factura) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Consulta SQL para obtener una factura específica por su ID
        $consulta_sql = "SELECT ef.id, ef.cedula_cliente, ef.fecha, ef.total, c.nombre as cliente_nombre, c.telefono 
                        FROM encabezado_factura ef 
                        LEFT JOIN cliente c ON ef.cedula_cliente = c.cedula 
                        WHERE ef.id = ?";

        // Prepara la consulta SQL
        $consulta = $conexion->prepare($consulta_sql);
        $consulta->bindValue(1, $id_factura);  // Asocia el valor del ID de factura

        // Ejecuta la consulta
        $consulta->execute();

        // Retorna el resultado como un array asociativo
        return $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene facturas por cliente
    public function obtener_facturas_por_cliente($cedula_cliente) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Consulta SQL para obtener facturas por cliente
        $consulta_sql = "SELECT ef.id, ef.cedula_cliente, ef.fecha, ef.total, c.nombre as cliente_nombre 
                        FROM encabezado_factura ef 
                        LEFT JOIN cliente c ON ef.cedula_cliente = c.cedula 
                        WHERE ef.cedula_cliente = ? 
                        ORDER BY ef.fecha DESC";

        // Prepara la consulta SQL
        $consulta = $conexion->prepare($consulta_sql);
        $consulta->bindValue(1, $cedula_cliente);

        // Ejecuta la consulta
        $consulta->execute();

        // Retorna el resultado como un array asociativo
        return $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtiene facturas por rango de fechas
    public function obtener_facturas_por_fecha($fecha_inicio, $fecha_fin) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Consulta SQL para obtener facturas por rango de fechas
        $consulta_sql = "SELECT ef.id, ef.cedula_cliente, ef.fecha, ef.total, c.nombre as cliente_nombre 
                        FROM encabezado_factura ef 
                        LEFT JOIN cliente c ON ef.cedula_cliente = c.cedula 
                        WHERE ef.fecha BETWEEN ? AND ? 
                        ORDER BY ef.fecha DESC";

        // Prepara la consulta SQL
        $consulta = $conexion->prepare($consulta_sql);
        $consulta->bindValue(1, $fecha_inicio);
        $consulta->bindValue(2, $fecha_fin);

        // Ejecuta la consulta
        $consulta->execute();

        // Retorna el resultado como un array asociativo
        return $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Inserta una nueva factura
    public function insertar_factura($cedula_cliente, $fecha, $total) {
        try {
            // Establece la conexión a la base de datos
            $conexion = parent::conectar_bd();
            parent::establecer_codificacion();
            
            // Sentencia SQL para insertar una nueva factura
            $sentencia_sql = "INSERT INTO encabezado_factura(id, cedula_cliente, fecha, total) VALUES (NULL, ?, ?, ?)";

            // Prepara la sentencia SQL
            $sentencia = $conexion->prepare($sentencia_sql);
            $sentencia->bindValue(1, $cedula_cliente);  // Asocia la cédula del cliente
            $sentencia->bindValue(2, $fecha);  // Asocia la fecha
            $sentencia->bindValue(3, $total);  // Asocia el total

            // Ejecuta la sentencia
            $sentencia->execute();

            // Retorna el ID de la factura insertada
            return $conexion->lastInsertId();
            
        } catch (Exception $e) {
            error_log("Error en insertar_factura: " . $e->getMessage());
            return false;
        }
    }

    // Actualiza una factura existente
    public function actualizar_factura($id_factura, $cedula_cliente, $fecha, $total) {
        // Establece la conexión a la base de datos
        $conexion = parent::conectar_bd();
        parent::establecer_codificacion();
        
        // Sentencia SQL para actualizar una factura existente
        $sentencia_sql = "UPDATE encabezado_factura SET cedula_cliente = ?, fecha = ?, total = ? WHERE id = ?";

        // Prepara la sentencia SQL
        $sentencia = $conexion->prepare($sentencia_sql);
        $sentencia->bindValue(1, $cedula_cliente);  // Asocia la cédula del cliente
        $sentencia->bindValue(2, $fecha);  // Asocia la fecha
        $sentencia->bindValue(3, $total);  // Asocia el total
        $sentencia->bindValue(4, $id_factura);   // Asocia el ID de la factura a actualizar

        // Ejecuta la sentencia
        $sentencia->execute();

        // Retorna el resultado
        return $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    // Elimina una factura (también eliminará los detalles por FK)
    public function eliminar_factura($id_factura) {
        try {
            // Establece la conexión a la base de datos
            $conexion = parent::conectar_bd();
            parent::establecer_codificacion();
            
            // Iniciar transacción
            $conexion->beginTransaction();
            
            // Primero eliminar los detalles de la factura
            $sentencia_detalle = $conexion->prepare("DELETE FROM detalle_factura WHERE id_factura = ?");
            $sentencia_detalle->bindValue(1, $id_factura);
            $sentencia_detalle->execute();
            
            // Luego eliminar el encabezado de la factura
            $sentencia_encabezado = $conexion->prepare("DELETE FROM encabezado_factura WHERE id = ?");
            $sentencia_encabezado->bindValue(1, $id_factura);
            $sentencia_encabezado->execute();
            
            // Confirmar transacción
            $conexion->commit();
            
            return ["mensaje" => "Factura eliminada correctamente"];
            
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $conexion->rollback();
            error_log("Error en eliminar_factura: " . $e->getMessage());
            return false;
        }
    }

}
?>