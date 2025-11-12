<?php

// Establece el tipo de contenido a JSON
header("Content-Type: application/json");

// Incluye los archivos necesarios para la conexión a la base de datos y la clase Usuario
require_once("../configuracion/conexion.php");
require_once("../modelos/Usuario.php");

// Configuración de la clave de cifrado (compartida)
define("CLAVE_SECRETA", "0123456789abcdef0123456789abcdef");

// Crea una instancia de la clase Usuario para verificar el KEY
$usuario = new Usuario();

// Verifica si el encabezado `KEY` está presente y es válido en la base de datos
$encabezados = getallheaders();
if (!isset($encabezados['KEY'])) {
    echo json_encode(["error" => "KEY no proporcionado"]);
    exit();
}

// Debug: Mostrar el KEY recibido
error_log("KEY recibido: " . $encabezados['KEY']);

// Verifica si el KEY existe en la tabla usuario
$key_valido = $usuario->verificar_key_usuario($encabezados['KEY']);
error_log("Resultado verificación KEY: " . ($key_valido ? 'true' : 'false'));

if (!$key_valido) {
    echo json_encode(["error" => "KEY no válido - Acceso no autorizado", "key_recibido" => $encabezados['KEY']]);
    exit();
}

// Si el KEY es válido, obtener los datos del usuario
$datos_usuario = $usuario->obtener_usuario_por_key($encabezados['KEY']);
if (!empty($datos_usuario)) {
    echo json_encode($datos_usuario);
    exit();
}

// Función para desencriptar datos en formato JSON con AES-256-ECB
function Desencriptar_BODY($JSON) {
    // Definir el tipo de cifrado (AES-256-ECB)
    $cifrado = "aes-256-ecb";
    
    // Desencriptar usando openssl_decrypt
    $JSON_desencriptado = openssl_decrypt(base64_decode($JSON), $cifrado, CLAVE_SECRETA, OPENSSL_RAW_DATA);
    
    // Verificar si la desencriptación falló
    if ($JSON_desencriptado === false) {
        // Devolver un mensaje de error si la desencriptación falla
        return false;
    }

    // Devolver los datos desencriptados
    return $JSON_desencriptado;
}

// Crea una instancia de la clase Usuario
$usuario = new Usuario();

// Obtiene y desencripta el JSON enviado en el BODY
$body_encriptado = file_get_contents("php://input");
$body = json_decode(Desencriptar_BODY($body_encriptado), true);

// Si no se pudo desencriptar el JSON, devuelve un error
if ($body === null) {
    echo json_encode(["Error" => "Error al desencriptar los datos."]);
    exit();
}

// Define las operaciones basadas en el parámetro "op" de la URL
switch ($_GET["op"]) {

    // Obtiene todos los usuarios
    case "ObtenerTodos":
        // Llama al método para obtener todos los usuarios
        $datos = $usuario->obtener_usuarios();
        // Devuelve los datos en formato JSON
        echo json_encode($datos);
        break;

    // Obtiene un usuario por su cédula
    case "ObtenerPorCedula":
        // Llama al método para obtener un usuario específico por cédula
        $datos = $usuario->obtener_usuario_por_cedula($body["cedula"]);   
        // Devuelve los datos del usuario en formato JSON
        echo json_encode($datos);   
        break;

    // Inserta un nuevo usuario
    case "Insertar":
        // Llama al método para insertar un nuevo usuario
        $datos = $usuario->insertar_usuario($body["cedula"], $body["nombre"], $body["llave"]);
        // Devuelve una respuesta indicando que la inserción fue correcta
        echo json_encode(["Correcto" => "Inserción Realizada"]);
        break;

    // Actualiza un usuario existente
    case "Actualizar":
        // Llama al método para actualizar un usuario existente
        $datos = $usuario->actualizar_usuario($body["cedula"], $body["nombre"], $body["llave"]);
        // Devuelve una respuesta indicando que la actualización fue correcta
        echo json_encode(["Correcto" => "Actualización Realizada"]);
        break;

    // Elimina un usuario
    case "Eliminar":
        // Llama al método para eliminar un usuario
        $datos = $usuario->eliminar_usuario($body["cedula"]);
        // Devuelve una respuesta indicando que la eliminación fue correcta
        echo json_encode(["Correcto" => "Eliminación Realizada"]);
        break;

    // Verifica credenciales de usuario (login)
    case "Login":
        // Llama al método para verificar las credenciales
        $datos = $usuario->verificar_usuario($body["cedula"], $body["nombre"]);
        if ($datos) {
            echo json_encode(["Correcto" => "Login exitoso", "usuario" => $datos]);
        } else {
            echo json_encode(["Error" => "Credenciales incorrectas"]);
        }
        break;
}
?>