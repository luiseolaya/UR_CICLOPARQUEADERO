<?php

namespace App\Controllers;

use App\Config\Database;
use App\Models\ModeloEvidencia;
use PDO;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/../models/ModeloEvidencia.php';

class EvidenciaController {
    private $db;
    private $evidencia;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->evidencia = new ModeloEvidencia($this->db);
    }

    public function subirEvidencia() {
        if (!isset($_SESSION['id_usuario'])) {
            $_SESSION['error'] = 'Debe iniciar sesión para registrar una entrada.';
            header("Location: /UR_CICLOPARQUEADERO/app/views/inicio_sesion.php");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['evidencia'])) {
            $foto = $_POST['evidencia'];
            $foto = str_replace('data:image/png;base64,', '', $foto);
            $foto = base64_decode($foto);

            // Asignar valores al modelo
            $this->evidencia->id_usuario = $_SESSION['id_usuario'];
            $this->evidencia->evidencia = $foto;

            // Intentar crear la evidencia
            if ($this->evidencia->crearEvidencia()) {
                $_SESSION['mensaje'] = "Foto subida con éxito.";
                header("Location: /UR_CICLOPARQUEADERO/inc_user/");
                exit;
            } else {
                $_SESSION['error'] = 'Error al registrar la evidencia.';
                header("Location: /UR_CICLOPARQUEADERO/reg_entrada");
                exit;
            }
        } else {
            $_SESSION['error'] = 'Datos de evidencia no encontrados.';
            header("Location: /UR_CICLOPARQUEADERO/reg_entrada");
            exit;
        }
    }
}

if (isset($_POST['subir_evidencia'])) {
    $evidenciaController = new EvidenciaController();
    $evidenciaController->subirEvidencia();
}
 