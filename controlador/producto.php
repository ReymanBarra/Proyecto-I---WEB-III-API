<?php

// Establece el tipo de contenido a JSON
header("Content-Type: application/json");

// Incluye los archivos necesarios para la conexión a la base de datos y la clase Producto
require_once("../configuracion/conexion.php");
require_once("../modelos/Producto.php");
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

// Crea una instancia de la clase Producto
$producto = new Producto();

// Obtiene el JSON enviado en el BODY (sin encriptar)
$body_json = file_get_contents("php://input");
$body = json_decode($body_json, true);

// Si no se pudo decodificar el JSON, usar array vacío
if ($body === null) {
    $body = [];
}

// Define las operaciones basadas en el parámetro "op" de la URL
switch ($_GET["op"]) {

    // Obtiene todos los productos
    case "ObtenerTodos":
        // Llama al método para obtener todos los productos
        $datos = $producto->obtener_productos();
        // Devuelve los datos en formato JSON
        echo json_encode($datos);
        break;

    // Obtiene un producto por su código
    case "ObtenerPorCodigo":
        // Llama al método para obtener un producto específico por código
        $datos = $producto->obtener_producto_por_codigo($body["codigo"]);   
        // Devuelve los datos del producto en formato JSON
        echo json_encode($datos);   
        break;

    // Obtiene productos por categoría
    case "ObtenerPorCategoria":
        // Llama al método para obtener productos por categoría
        $datos = $producto->obtener_productos_por_categoria($body["id_categoria"]);   
        // Devuelve los datos de los productos en formato JSON
        echo json_encode($datos);   
        break;

    // Inserta un nuevo producto
    case "Insertar":
        // Llama al método para insertar un nuevo producto
        $datos = $producto->insertar_producto($body["codigo"], $body["nombre"], $body["precio"], $body["id_categoria"]);
        // Devuelve una respuesta indicando que la inserción fue correcta
        echo json_encode(["Correcto" => "Inserción Realizada"]);
        break;

    // Actualiza un producto existente
    case "Actualizar":
        // Llama al método para actualizar un producto existente
        $datos = $producto->actualizar_producto($body["codigo"], $body["nombre"], $body["precio"], $body["id_categoria"]);
        // Devuelve una respuesta indicando que la actualización fue correcta
        echo json_encode(["Correcto" => "Actualización Realizada"]);
        break;

    // Elimina un producto
    case "Eliminar":
        // Llama al método para eliminar un producto
        $datos = $producto->eliminar_producto($body["codigo"]);
        // Devuelve una respuesta indicando que la eliminación fue correcta
        echo json_encode(["Correcto" => "Eliminación Realizada"]);
        break;
}
?>