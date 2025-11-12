<?php

// Establece el tipo de contenido a JSON
header("Content-Type: application/json");

// Incluye los archivos necesarios para la conexión a la base de datos y la clase Cliente
require_once("../configuracion/conexion.php");
require_once("../modelos/Cliente.php");
require_once("../modelos/Categoria.php");

// Configuración de la clave de cifrado (compartida)
define("CLAVE_SECRETA", "0123456789abcdef0123456789abcdef");

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

// Crea una instancia de la clase Cliente
$cliente = new Cliente();

// Obtiene el JSON enviado en el BODY (sin encriptar)
$body_json = file_get_contents("php://input");
$body = json_decode($body_json, true);

// Si no se pudo decodificar el JSON, usar array vacío
if ($body === null) {
    $body = [];
}

// Define las operaciones basadas en el parámetro "op" de la URL
switch ($_GET["op"]) {

    // Obtiene todos los clientes
    case "ObtenerTodos":
        // Llama al método para obtener todos los clientes
        $datos = $cliente->obtener_clientes();
        // Devuelve los datos en formato JSON
        echo json_encode($datos);
        break;

    // Obtiene un cliente por su cédula
    case "ObtenerPorCedula":
        // Llama al método para obtener un cliente específico por cédula
        $datos = $cliente->obtener_cliente_por_cedula($body["cedula"]);   
        // Devuelve los datos del cliente en formato JSON
        echo json_encode($datos);   
        break;

    // Busca clientes por nombre
    case "BuscarPorNombre":
        // Llama al método para buscar clientes por nombre
        $datos = $cliente->buscar_clientes_por_nombre($body["nombre"]);   
        // Devuelve los datos de los clientes en formato JSON
        echo json_encode($datos);   
        break;

    // Inserta un nuevo cliente
    case "Insertar":
        // Llama al método para insertar un nuevo cliente
        $telefono = isset($body["telefono"]) ? $body["telefono"] : null;
        $datos = $cliente->insertar_cliente($body["cedula"], $body["nombre"], $telefono);
        // Devuelve una respuesta indicando que la inserción fue correcta
        echo json_encode(["Correcto" => "Inserción Realizada"]);
        break;

    // Actualiza un cliente existente
    case "Actualizar":
        // Llama al método para actualizar un cliente existente
        $telefono = isset($body["telefono"]) ? $body["telefono"] : null;
        $datos = $cliente->actualizar_cliente($body["cedula"], $body["nombre"], $telefono);
        // Devuelve una respuesta indicando que la actualización fue correcta
        echo json_encode(["Correcto" => "Actualización Realizada"]);
        break;

    // Elimina un cliente
    case "Eliminar":
        // Llama al método para eliminar un cliente
        $datos = $cliente->eliminar_cliente($body["cedula"]);
        // Devuelve una respuesta indicando que la eliminación fue correcta
        echo json_encode(["Correcto" => "Eliminación Realizada"]);
        break;
}
?>