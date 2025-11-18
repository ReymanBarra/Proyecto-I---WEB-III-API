<?php

// Establece el tipo de contenido a JSON
header("Content-Type: application/json");

// Incluye los archivos necesarios para la conexión a la base de datos y la clase Producto
require_once("../configuracion/conexion.php");
require_once("../modelos/Producto.php");
require_once("../modelos/Categoria.php");

// Crea una instancia de la clase Categoria para verificar el KEY


/* ===========================================
   VALIDACIÓN DEL HEADER KEY (CORREGIDA)


// 4. Validar el KEY en la base de datos
$key_valido = $categoria->verificar_key_usuario($KEY);

if (!$key_valido) {
    echo json_encode(["error" => "KEY no válido - Acceso no autorizado"], JSON_UNESCAPED_UNICODE);
    exit();
}

/* ===========================================
   FIN DE VALIDACIÓN DEL KEY
   =========================================== */


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
        $datos = $producto->obtener_productos();
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    // Obtiene un producto por su código
    case "ObtenerPorCodigo":
        $datos = $producto->obtener_producto_por_codigo($body["codigo"]);
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    // Obtiene productos por categoría
    case "ObtenerPorCategoria":
        $datos = $producto->obtener_productos_por_categoria($body["id_categoria"]);
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    // Inserta un nuevo producto
    case "Insertar":
        $producto->insertar_producto($body["codigo"], $body["nombre"], $body["precio"], $body["id_categoria"]);
        echo json_encode(["Correcto" => "Inserción Realizada"], JSON_UNESCAPED_UNICODE);
        break;

    // Actualiza un producto existente
    case "Actualizar":
        $producto->actualizar_producto($body["codigo"], $body["nombre"], $body["precio"], $body["id_categoria"]);
        echo json_encode(["Correcto" => "Actualización Realizada"], JSON_UNESCAPED_UNICODE);
        break;

    // Elimina un producto
    case "Eliminar":
        $producto->eliminar_producto($body["codigo"]);
        echo json_encode(["Correcto" => "Eliminación Realizada"], JSON_UNESCAPED_UNICODE);
        break;
}

?>
