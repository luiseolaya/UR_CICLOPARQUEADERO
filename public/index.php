<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

use App\Config\Database;
use App\Controllers\UsuarioController;
use App\Controllers\EntradaController;
use App\Controllers\LogoutController;
use App\Controllers\EvidenciaController;
use App\Controllers\ReporteController;

require_once $_SERVER['DOCUMENT_ROOT'] . '/UR_CICLOPARQUEADERO/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/UR_CICLOPARQUEADERO/app/controllers/UsuarioController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/UR_CICLOPARQUEADERO/app/controllers/EntradaController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/UR_CICLOPARQUEADERO/app/controllers/LogoutController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/UR_CICLOPARQUEADERO/app/controllers/ReporteController.php';

$request = $_SERVER['REQUEST_URI'];
$request = str_replace("/UR_CICLOPARQUEADERO/", "", $request);
$parsed_url = parse_url($request);
$path = isset($parsed_url['path']) ? trim($parsed_url['path'], '/') : '';
$query = isset($parsed_url['query']) ? $parsed_url['query'] : '';
parse_str($query, $params);
error_log("Path: " . $path);

if (isset($params['registrar_entrada'])) {
    $controller = new EntradaController();
    $controller->registrarEntrada();
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
            require $_SERVER['DOCUMENT_ROOT'] . '/UR_CICLOPARQUEADERO/app/views/inicio.php';
            break;
        case 'inc_user':
            require $_SERVER['DOCUMENT_ROOT'] . '/UR_CICLOPARQUEADERO/app/views/inc_user.php';
            break;
        case 'admin_inc':
            require $_SERVER['DOCUMENT_ROOT'] . '/UR_CICLOPARQUEADERO/app/views/admin_inc.php';
            break;
        case 'reg_entrada':
            require $_SERVER['DOCUMENT_ROOT'] . '/UR_CICLOPARQUEADERO/app/views/reg_entrada.php';
            break;
        case 'evidencia':
            require $_SERVER['DOCUMENT_ROOT'] . '/UR_CICLOPARQUEADERO/app/views/evidencia.php';
            break;
        case 'edit_user':
            require $_SERVER['DOCUMENT_ROOT'] . '/UR_CICLOPARQUEADERO/app/views/Edit_user.php';
            break;
        case 'view_ent_user':
            require $_SERVER['DOCUMENT_ROOT'] . '/UR_CICLOPARQUEADERO/app/views/view_ent_user.php';
            break;
        default:
            http_response_code(404);
            echo "Página no encontrada";
            break;
    }
}
?>