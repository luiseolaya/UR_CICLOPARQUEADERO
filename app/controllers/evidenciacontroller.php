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
            $_SESSION['error'] = 'Primero debes iniciar sesión para registrar una entrada.';
            header("Location: /UR_CICLOPARQUEADERO/");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['foto'])) {
            $foto = $_POST['foto'];
            $foto = str_replace('data:image/png;base64,', '', $foto);
            $foto = base64_decode($foto);

            // Asignar valores al modelo
            $this->evidencia->id_usuario = $_SESSION['id_usuario'];
            $this->evidencia->foto = $foto; 

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

// Manejar la subida de evidencia
if (isset($_POST['subir_evidencia'])) {
    $evidenciaController = new EvidenciaController();
    $evidenciaController->subirEvidencia();
}

// Manejar la visualización de evidencia (ejemplo de uso)
if (isset($_GET['action']) && $_GET['action'] === 'mostrar' && isset($_GET['id_parqueadero'])) {
    $evidenciaController = new EvidenciaController();
    $evidenciaController->mostrarEvidencia($_GET['id_parqueadero']);
}