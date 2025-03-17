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

    public function mostrarEvidencia($id_parqueadero) {
        // Preparar y ejecutar la consulta
        $stmt = $this->db->prepare("SELECT foto FROM evidencia WHERE id_parqueadero = ?");
        $stmt->bindParam(1, $id_parqueadero, PDO::PARAM_INT);
        $stmt->execute();
        $imagen = $stmt->fetchColumn();

        if ($imagen) {
            // Mostrar la imagen
            header("Content-Type: image/png");
            echo $imagen;
        } else {
            echo "Imagen no encontrada.";
        }
    }

    public function mostrarEvidencias() {
        if (!isset($_SESSION['id_usuario'])) {
            $_SESSION['error'] = 'Primero debes iniciar sesión para ver las evidencias.';
            header("Location: /UR_CICLOPARQUEADERO/");
            exit;
        }

        $evidencias = $this->evidencia->obtenerEvidenciasPorUsuario($_SESSION['id_usuario']);
        return $evidencias;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'mostrar' && isset($_GET['id_parqueadero'])) {
    $evidenciaController = new EvidenciaController();
    $evidenciaController->mostrarEvidencia($_GET['id_parqueadero']);
}