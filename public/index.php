<?php

ini_set('log_errors', 1);
ini_set('error_log', $_SERVER['DOCUMENT_ROOT'] . '/cicloparqueaderos/log.txt');
error_log("Inicio de ejecución en index.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

use App\Config\Database;
use App\Controllers\UsuarioController;
use App\Controllers\EntradaController;
use App\Controllers\LogoutController;
use App\Controllers\EvidenciaController;
use App\Controllers\ReporteController;

require_once $_SERVER['DOCUMENT_ROOT'] . '/cicloparqueaderos/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/cicloparqueaderos/app/controllers/UsuarioController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/cicloparqueaderos/app/controllers/EntradaController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/cicloparqueaderos/app/controllers/LogoutController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/cicloparqueaderos/app/controllers/ReporteController.php';

$request = $_SERVER['REQUEST_URI'];
$request = str_replace("/cicloparqueaderos/", "", $request);
$parsed_url = parse_url($request);
$path = isset($parsed_url['path']) ? trim($parsed_url['path'], '/') : '';
$query = isset($parsed_url['query']) ? $parsed_url['query'] : '';
parse_str($query, $params);
error_log("Path: " . $path);

if (isset($_POST['registrar_entrada'])) {
    $entradaController = new EntradaController();
    $entradaController->registrarEntrada();
}

if (isset($params['subir_evidencia'])) {
    error_log("solicitud de subir evidencia recibida", 3, $_SERVER['DOCUMENT_ROOT'] . '/cicloparqueaderos/log.txt');
    $controller = new EntradaController();
    $controller->subirEvidencia();
    exit; // Detén la ejecución para evitar duplicidad
} elseif (isset($params['logout'])) {
    $controller = new LogoutController();
    $controller->logout();
} elseif (isset($params['generar_reporte'])) {
    
    ob_clean();
    $controller = new ReporteController();
    $controller->generarReporte();
    exit; 
} else {
    switch ($path) {
        case '':
        case 'index.php':
            require $_SERVER['DOCUMENT_ROOT'] . '/cicloparqueaderos/app/views/inicio.php';
            break;
        case 'inicio':
            require $_SERVER['DOCUMENT_ROOT'] . '/cicloparqueaderos/app/views/inc_user.php';
            break;
        case 'ADMINISTRADOR':
            require $_SERVER['DOCUMENT_ROOT'] . '/cicloparqueaderos/app/views/admin_inc.php';
            break;
        case 'reg_entrada':
            require $_SERVER['DOCUMENT_ROOT'] . '/cicloparqueaderos/app/views/reg_entrada.php';
            break;
        case 'evidencia':
            require $_SERVER['DOCUMENT_ROOT'] . '/cicloparqueaderos/app/views/evidencia.php';
            break;
        case 'edit_user':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_usuario'])) {
                $controller = new UsuarioController();
                $controller->actualizarUsuario();
            } else {
                require $_SERVER['DOCUMENT_ROOT'] . '/cicloparqueaderos/app/views/edit_user.php';
            }
            break;
        case 'view_ent_user':
            require $_SERVER['DOCUMENT_ROOT'] . '/cicloparqueaderos/app/views/view_ent_user.php';
            break;
        case 'TERMINOS': 
            require $_SERVER['DOCUMENT_ROOT'] . '/cicloparqueaderos/app/views/cel.php';
            break;
        case 'actualizar_telefono':
            require $_SERVER['DOCUMENT_ROOT'] . '/cicloparqueaderos/app/views/ac_cel.php';
            break;
        default:
            http_response_code(404);
            echo "Página no encontrada";
            break;
    }
}
?>