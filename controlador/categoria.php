<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

// Preflight request handling
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
// Establece el tipo de contenido a JSON
 header("Content-Type: application/json");

// Incluye los archivos necesarios para la conexión a la base de datos y la clase Categoria
require_once("../configuracion/conexion.php");
require_once("../modelos/Categoria.php");

// Crea una instancia de la clase Categoria
$categoria = new Categoria();

// Obtiene el JSON enviado en el BODY (sin encriptar)
$body_json = file_get_contents("php://input");
$body = json_decode($body_json, true);

// Si no se pudo decodificar el JSON, usar array vacío
if ($body === null) {
    $body = [];
}

// Define las operaciones basadas en el método HTTP o el parámetro "op" de la URL
$metodo_http = $_SERVER['REQUEST_METHOD'];
$operacion = isset($_GET["op"]) ? $_GET["op"] : null;

// Si hay parámetro ?op=, usamos el sistema original
if ($operacion) {
    switch ($operacion) {
        // Obtiene todas las categorías activas
        case "ObtenerTodas":
            // Llama al método para obtener todas las categorías
            $datos = $categoria->obtener_categorias();
            // Devuelve los datos en formato JSON
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);
            break;

        // Obtiene una categoría por su ID
        case "ObtenerPorId":
            // Llama al método para obtener una categoría específica por ID
            $datos = $categoria->obtener_categoria_por_id($body["id"]);   
            // Devuelve los datos de la categoría en formato JSON
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);   
            break;

        // Inserta una nueva categoría
        case "Insertar":
            // Llama al método para insertar una nueva categoría
            $datos = $categoria->insertar_categoria($body["nombre"]);
            // Devuelve una respuesta indicando que la inserción fue correcta
            echo json_encode(["Correcto" => "Inserción Realizada"], JSON_UNESCAPED_UNICODE);
            break;

        // Actualiza una categoría existente
        case "Actualizar":
            // Llama al método para actualizar una categoría existente
            $datos = $categoria->actualizar_categoria($body["id"], $body["nombre"]);
            // Devuelve una respuesta indicando que la actualización fue correcta
            echo json_encode(["Correcto" => "Actualización Realizada"], JSON_UNESCAPED_UNICODE);
            break;

        // Elimina una categoría
        case "Eliminar":
            // Llama al método para eliminar una categoría
            $datos = $categoria->eliminar_categoria($body["id"]);
            // Devuelve una respuesta indicando que la eliminación fue correcta
            echo json_encode(["Correcto" => "Eliminación Realizada"], JSON_UNESCAPED_UNICODE);
            break;
    }
} else {
    // Sistema de métodos HTTP estándar (CRUD #3)
    switch ($metodo_http) {
        
        case 'GET':
            // GET: Obtener categorías
            if (isset($_GET['id'])) {
                // GET con ID: obtener categoría específica
                $datos = $categoria->obtener_categoria_por_id($_GET['id']);
                if (!empty($datos)) {
                    echo json_encode($datos[0], JSON_UNESCAPED_UNICODE); // Devolver solo el objeto, no array
                } else {
                    http_response_code(404);
                    echo json_encode(["error" => "Categoría no encontrada"], JSON_UNESCAPED_UNICODE);
                }
            } else {
                // GET sin ID: obtener todas las categorías
                $datos = $categoria->obtener_categorias();
                echo json_encode($datos, JSON_UNESCAPED_UNICODE);
            }
            break;

        case 'POST':
            // POST: Crear nueva categoría
            if (isset($body["nombre"])) {
                $categoria->insertar_categoria($body["nombre"]);
                http_response_code(201);
                echo json_encode(["mensaje" => "Categoría creada exitosamente"], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Datos incompletos - se requiere 'nombre'"], JSON_UNESCAPED_UNICODE);
            }
            break;

        case 'PUT':
            // PUT: Actualizar categoría existente
            if (isset($_GET['id']) && isset($body["nombre"])) {
                $resultado = $categoria->actualizar_categoria($_GET['id'], $body["nombre"]);
                echo json_encode(["mensaje" => "Categoría actualizada exitosamente"], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Datos incompletos - se requieren ID y nombre"], JSON_UNESCAPED_UNICODE);
            }
            break;

        case 'DELETE':
            // DELETE: Eliminar categoría
            if (isset($_GET['id'])) {
                $categoria->eliminar_categoria($_GET['id']);
                echo json_encode(["mensaje" => "Categoría eliminada exitosamente"], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Se requiere ID para eliminar"], JSON_UNESCAPED_UNICODE);
            }
            break;

        default:
            // Método no soportado
            http_response_code(405);
            echo json_encode(["error" => "Método HTTP no soportado"], JSON_UNESCAPED_UNICODE);
            break;
    }
}
?>