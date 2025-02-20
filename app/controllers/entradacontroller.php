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
            header("Location: /UR_CICLOPARQUEADERO/inicio_sesion");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_parqueadero'])) {
            // Asignar valores al modelo
            $this->entrada->id_usuario = $_SESSION['id_usuario'];
            $this->entrada->id_parqueadero = $_POST['id_parqueadero'];
            $this->entrada->fecha_hora = date('Y-m-d H:i:s');

            // Intentar crear la entrada
            if ($this->entrada->crearEntrada()) {
                $_SESSION['mensaje'] = "Entrada registrada con éxito.";
                header("Location: /UR_CICLOPARQUEADERO/evidencia/");
                exit;
            } else {
                $_SESSION['error'] = 'Error al registrar la entrada.';
                header("Location: /UR_CICLOPARQUEADERO/reg_entrada");
                exit;
            }
        } else {
            $_SESSION['error'] = 'Datos de entrada no encontrados.';
            header("Location: /UR_CICLOPARQUEADERO/reg_entrada");
            exit;
        }
    }
}

if (isset($_GET['registrar_entrada'])) {
    $entradaController = new EntradaController();
    $entradaController->registrarEntrada();
}