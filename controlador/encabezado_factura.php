<?php

// Establece el tipo de contenido a JSON
header("Content-Type: application/json");

// Incluye los archivos necesarios para la conexión a la base de datos y la clase EncabezadoFactura
require_once("../configuracion/conexion.php");
require_once("../modelos/EncabezadoFactura.php");
require_once("../modelos/Categoria.php");

// Crea una instancia de la clase Categoria para verificar el KEY
$categoria = new Categoria();

// Verifica si el encabezado `KEY` está presente y es válido en la base de datos
$encabezados = getallheaders();
if (!isset($encabezados['KEY'])) {
    echo json_encode(["error" => "KEY no proporcionado"]);
    exit();
}

// Verifica si el KEY existe en la tabla usuario
$key_valido = $categoria->verificar_key_usuario($encabezados['KEY']);

if (!$key_valido) {
    echo json_encode(["error" => "KEY no válido - Acceso no autorizado"]);
    exit();
}

// Crea una instancia de la clase EncabezadoFactura
$factura = new EncabezadoFactura();

// Obtiene el JSON enviado en el BODY (sin encriptar)
$body_json = file_get_contents("php://input");
$body = json_decode($body_json, true);

// Si no se pudo decodificar el JSON, usar array vacío
if ($body === null) {
    $body = [];
}

// Define las operaciones basadas en el parámetro "op" de la URL
switch ($_GET["op"]) {

    // Obtiene todas las facturas
    case "ObtenerTodas":
        // Llama al método para obtener todas las facturas
        $datos = $factura->obtener_facturas();
        // Devuelve los datos en formato JSON
        echo json_encode($datos);
        break;

    // Obtiene una factura por su ID
    case "ObtenerPorId":
        // Llama al método para obtener una factura específica por ID
        $datos = $factura->obtener_factura_por_id($body["id"]);   
        // Devuelve los datos de la factura en formato JSON
        echo json_encode($datos);   
        break;

    // Obtiene facturas por cliente
    case "ObtenerPorCliente":
        // Llama al método para obtener facturas por cliente
        $datos = $factura->obtener_facturas_por_cliente($body["cedula_cliente"]);   
        // Devuelve los datos de las facturas en formato JSON
        echo json_encode($datos);   
        break;

    // Obtiene facturas por rango de fechas
    case "ObtenerPorFecha":
        // Llama al método para obtener facturas por rango de fechas
        $datos = $factura->obtener_facturas_por_fecha($body["fecha_inicio"], $body["fecha_fin"]);   
        // Devuelve los datos de las facturas en formato JSON
        echo json_encode($datos);   
        break;

    // Inserta una nueva factura
    case "Insertar":
        // Llama al método para insertar una nueva factura
        $id_factura = $factura->insertar_factura($body["cedula_cliente"], $body["fecha"], $body["total"]);
        if ($id_factura) {
            echo json_encode(["Correcto" => "Inserción Realizada", "id_factura" => $id_factura]);
        } else {
            echo json_encode(["Error" => "Error al insertar factura"]);
        }
        break;

    // Actualiza una factura existente
    case "Actualizar":
        // Llama al método para actualizar una factura existente
        $datos = $factura->actualizar_factura($body["id"], $body["cedula_cliente"], $body["fecha"], $body["total"]);
        // Devuelve una respuesta indicando que la actualización fue correcta
        echo json_encode(["Correcto" => "Actualización Realizada"]);
        break;

    // Elimina una factura
    case "Eliminar":
        // Llama al método para eliminar una factura
        $resultado = $factura->eliminar_factura($body["id"]);
        if ($resultado) {
            echo json_encode(["Correcto" => "Eliminación Realizada"]);
        } else {
            echo json_encode(["Error" => "Error al eliminar factura"]);
        }
        break;
}
?>