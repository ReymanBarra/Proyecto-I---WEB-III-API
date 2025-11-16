<?php
// servicios/usuario.php
header("Content-Type: application/json");

try {
    require_once(__DIR__ . "/../configuracion/conexion.php");
    require_once(__DIR__ . "/../modelos/Usuario.php");
    require_once(__DIR__ . "/../modelos/Api.php");




    $usuario = new Usuario();
    $Api = new Api();

    $method = $_SERVER['REQUEST_METHOD'];

    //========== 1. VALIDAR HEADER =============
    $codigo_header = $_SERVER['HTTP_CODIGO'] ?? null;

    if (!$codigo_header) {
        echo json_encode(["Error" => "Acceso no autorizado - Código requerido"], JSON_UNESCAPED_UNICODE);
        exit();
    }

    //========== 2. VERIFICAR LLAVE EN BD ============
    $verificacion = $Api->VerificarKEY($codigo_header);

    if (empty($verificacion)) {
        $desactivado = $Api->VerificarDesactivado($codigo_header);
        echo json_encode([
            "Error" => $desactivado ? "Credenciales desactivadas" : "Acceso no autorizado - Código inválido"
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // llave obtenida de la BD
    $llave = $verificacion[0]['llave'];

    //========== 3. DESENCRIPTAR BODY ===========
    function desencriptar_BODY($json, $llave)
    {
        $cifrado = "aes-256-ecb";
        $json_desencriptado = openssl_decrypt(
            base64_decode($json),
            $cifrado,
            $llave,
            OPENSSL_RAW_DATA
        );

        return $json_desencriptado !== false ? $json_desencriptado : false;
    }

    $body_encriptado = file_get_contents("php://input");
    $body = [];

    if (!empty($body_encriptado)) {
        $json_desencriptado = desencriptar_BODY($body_encriptado, $llave);
        $body = json_decode($json_desencriptado, true);

        if ($body === null) {
            echo json_encode(["Error" => "Error al desencriptar los datos"], JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    //========== VARIABLES DEL CRUD ===========
    $cedula = $body["cedula"] ?? ($_GET["cedula"] ?? "");
    $nombre = $body["nombre"] ?? "";
    $edad = $body["edad"] ?? "";
    $telefono = $body["telefono"] ?? "";

    //===================================================
    //                   CRUD
    //===================================================
    switch ($method) {

        //================ INSERTAR =================
        case "POST":
            $rspta = $usuario->insertar($cedula, $nombre, $edad, $telefono);
            if (intval($rspta) == 1) {
                echo json_encode(["Correcto" => "Usuario agregado"], JSON_UNESCAPED_UNICODE);
            } elseif (intval($rspta) == 1062) {
                echo json_encode(["Error" => "La cédula ya existe"], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(["Error" => "No se pudo agregar el usuario"], JSON_UNESCAPED_UNICODE);
            }
            break;

        //================ EDITAR =================
        case "PUT":
            $rspta = $usuario->editar($cedula, $nombre, $edad, $telefono);
            echo json_encode(
                $rspta
                ? ["Correcto" => "Usuario actualizado"]
                : ["Error" => "Usuario no se pudo actualizar"],
                JSON_UNESCAPED_UNICODE
            );
            break;

        //================ ELIMINAR =================
        case "DELETE":
            $rspta = $usuario->eliminar($cedula);
            echo json_encode(
                $rspta
                ? ["Correcto" => "Usuario eliminado"]
                : ["Error" => "Usuario no se pudo eliminar"],
                JSON_UNESCAPED_UNICODE
            );
            break;

        //================ LISTAR / MOSTRAR =================
        case "GET":

            if (!empty($cedula)) {
                $rspta = $usuario->mostrar($cedula);

                if (!empty($rspta) && isset($rspta->cedula)) {
                    echo json_encode([
                        "cedula" => $rspta->cedula,
                        "nombre" => $rspta->nombre,
                        "edad" => $rspta->edad,
                        "telefono" => $rspta->telefono
                    ], JSON_UNESCAPED_UNICODE);
                    break;
                }

                echo json_encode(["Error" => "Usuario no encontrado"], JSON_UNESCAPED_UNICODE);
                break;
            }

            // LISTAR TODOS
            $rspta = $usuario->listar();
            $data = [];

            while ($reg = $rspta->fetch(PDO::FETCH_OBJ)) {
                $data[] = [
                    "0" => $reg->cedula,
                    "1" => $reg->nombre,
                    "2" => $reg->edad,
                    "3" => $reg->telefono
                ];
            }

            echo json_encode([
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            ], JSON_UNESCAPED_UNICODE);
            break;

        default:
            http_response_code(405);
                echo json_encode(["Error" => "Método HTTP no permitido"], JSON_UNESCAPED_UNICODE);
            break;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "Error" => "Error interno",
        "Mensaje" => $e->getMessage(),
        "Archivo" => $e->getFile(),
        "Linea" => $e->getLine()
    ], JSON_UNESCAPED_UNICODE);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode([
        "Error" => "Error fatal",
        "Mensaje" => $e->getMessage(),
        "Archivo" => $e->getFile(),
        "Linea" => $e->getLine()
    ], JSON_UNESCAPED_UNICODE);
}
?>