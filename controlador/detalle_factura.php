<?php

// Establece el tipo de contenido a JSON
header("Content-Type: application/json");

// Incluye los archivos necesarios para la conexión a la base de datos y la clase DetalleFactura
require_once("../configuracion/conexion.php");
require_once("../modelos/DetalleFactura.php");
require_once("../modelos/Categoria.php");


// Crea una instancia de la clase Categoria para verificar el KEY
$categoria = new Categoria();

// Verifica si el encabezado `KEY` está presente y es válido en la base de datos
$encabezados = getallheaders();
if (!isset($encabezados['KEY'])) {
    echo json_encode(["error" => "KEY no proporcionado"], JSON_UNESCAPED_UNICODE);
    exit();
}

// Verifica si el KEY existe en la tabla usuario
$key_valido = $categoria->verificar_key_usuario($encabezados['KEY']);

if (!$key_valido) {
    echo json_encode(["error" => "KEY no válido - Acceso no autorizado"], JSON_UNESCAPED_UNICODE);
    exit();
}

// Crea una instancia de la clase DetalleFactura
$detalle = new DetalleFactura();

// Obtiene el JSON enviado en el BODY (sin encriptar)
$body_json = file_get_contents("php://input");
$body = json_decode($body_json, true);

// Si no se pudo decodificar el JSON, usar array vacío
if ($body === null) {
    $body = [];
}

// Define las operaciones basadas en el parámetro "op" de la URL
switch ($_GET["op"]) {

    // Obtiene todos los detalles
    case "ObtenerTodos":
        // Llama al método para obtener todos los detalles
        $datos = $detalle->obtener_detalles();
        // Devuelve los datos en formato JSON
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    // Obtiene un detalle por su ID
    case "ObtenerPorId":
        // Llama al método para obtener un detalle específico por ID
        $datos = $detalle->obtener_detalle_por_id($body["id_detalle"]);   
        // Devuelve los datos del detalle en formato JSON
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);   
        break;

    // Obtiene detalles por factura
    case "ObtenerPorFactura":
        // Llama al método para obtener detalles por factura
        $datos = $detalle->obtener_detalles_por_factura($body["id_factura"]);   
        // Devuelve los datos de los detalles en formato JSON
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);   
        break;

    // Obtiene productos más vendidos (reporte)
    case "ProductosMasVendidos":
        // Llama al método para obtener productos más vendidos
        $limite = isset($body["limite"]) ? $body["limite"] : 10;
        $datos = $detalle->obtener_productos_mas_vendidos($limite);   
        // Devuelve los datos del reporte en formato JSON
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);   
        break;

    // Calcula el total de una factura
    case "CalcularTotal":
        // Llama al método para calcular el total de una factura
        $total = $detalle->calcular_total_factura($body["id_factura"]);   
        // Devuelve el total calculado en formato JSON
        echo json_encode(["total" => $total], JSON_UNESCAPED_UNICODE);   
        break;

    // Inserta un nuevo detalle
    case "Insertar":
        // Llama al método para insertar un nuevo detalle
        $id_detalle = $detalle->insertar_detalle($body["id_factura"], $body["codigo_producto"], $body["precio_producto"], $body["cantidad_producto"]);
        if ($id_detalle) {
            echo json_encode(["Correcto" => "Inserción Realizada", "id_detalle" => $id_detalle], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["Error" => "Error al insertar detalle"], JSON_UNESCAPED_UNICODE);
        }
        break;

    // Actualiza un detalle existente
    case "Actualizar":
        // Llama al método para actualizar un detalle existente
        $datos = $detalle->actualizar_detalle($body["id_detalle"], $body["id_factura"], $body["codigo_producto"], $body["precio_producto"], $body["cantidad_producto"]);
        // Devuelve una respuesta indicando que la actualización fue correcta
        echo json_encode(["Correcto" => "Actualización Realizada"], JSON_UNESCAPED_UNICODE);
        break;

    // Elimina un detalle
    case "Eliminar":
        // Llama al método para eliminar un detalle
        $datos = $detalle->eliminar_detalle($body["id_detalle"]);
        // Devuelve una respuesta indicando que la eliminación fue correcta
        echo json_encode(["Correcto" => "Eliminación Realizada"], JSON_UNESCAPED_UNICODE);
        break;
}
?>