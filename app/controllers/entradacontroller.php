<?php 
namespace App\Controllers;

use App\Models\Entrada;
use App\Config\Database;
use PDO;
use DateTime;
use DateTimeZone;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/../models/Entrada.php';

class EntradaController {

    private $db;
    private $entrada;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->entrada = new Entrada($this->db);
    }

    public function registrarEntrada() {
        if (!isset($_SESSION['id_usuario'])) {
            $_SESSION['error'] = 'Debe iniciar sesión para registrar una entrada.';
            header("Location: /UR_CICLOPARQUEADERO/");
            exit;
        }

        if (!empty($_POST)) {
            $codigo_aleatorio = $_POST['codigo_aleatorio'];
            $color_aleatorio = $_POST['color_aleatorio'];
            $latUsuario = $_POST['lat_usuario'];
            $lngUsuario = $_POST['lng_usuario'];
            $idParqueadero = $_POST['id_parqueadero'];

            // Coordenadas de los parqueaderos
            $parqueaderos = [
                1 => ['lat' => 4.601025504132103, 'lng' => -74.07303884639771],  // Parqueadero A
                2 => ['lat' => 66.76717277970545, 'lng' => 175.597189200234],   // Parqueadero B
            ];

            if (!isset($parqueaderos[$idParqueadero])) {
                $_SESSION['error'] = 'Seleccione un parqueadero válido.';
                header("Location: /UR_CICLOPARQUEADERO/reg_entrada");
                exit;
            }

            $latParqueadero = $parqueaderos[$idParqueadero]['lat'];
            $lngParqueadero = $parqueaderos[$idParqueadero]['lng'];
            $rangoMaximo = 100000; // Cambiar según sea necesario

            if ($_POST['codigo'] !== $codigo_aleatorio || $_POST['color'] !== $color_aleatorio || $_POST['id_parqueadero'] == 'Seleccione el Cicloparqueadero') {
                $_SESSION['error'] = 'Ingrese de nuevo el código, color y parqueadero seleccionados.';
                header("Location: /UR_CICLOPARQUEADERO/reg_entrada");
                exit;
            }

            // Validar la distancia entre el usuario y el parqueadero
            $distancia = $this->calcularDistancia($latUsuario, $lngUsuario, $latParqueadero, $lngParqueadero);
            if ($distancia > $rangoMaximo) {
                $_SESSION['error'] = 'Estás fuera del rango permitido.';
                header("Location: /UR_CICLOPARQUEADERO/reg_entrada");
                exit;
            }

            $_SESSION['entrada_temp'] = $_POST;

            // Guardar la entrada en la base de datos
            $this->entrada->id_usuario = $_SESSION['id_usuario'];
            $this->entrada->id_parqueadero = $_POST['id_parqueadero'];
            $this->entrada->fecha_hora = date('Y-m-d H:i:s');
            $this->entrada->fecha_hora = (new DateTime('now', new DateTimeZone('America/Bogota')))->format('Y-m-d H:i:s');

            if ($this->entrada->crearEntrada()) {
                header("Location: /UR_CICLOPARQUEADERO/evidencia");
                exit;
            } else {
                $_SESSION['error'] = 'Error al registrar la entrada. Intente nuevamente.';
                header("Location: /UR_CICLOPARQUEADERO/reg_entrada");
                exit;
            }
        }
    }

    private function calcularDistancia($lat1, $lon1, $lat2, $lon2) {
        $radioTierra = 6371; // Radio de la Tierra en km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $radioTierra * $c;     }
}

if (isset($_POST['registrar_entrada'])) {
    $entradaController = new EntradaController();
    $entradaController->registrarEntrada();
}