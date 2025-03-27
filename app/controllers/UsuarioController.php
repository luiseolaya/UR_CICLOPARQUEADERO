<?php

namespace App\Controllers;

use App\Config\Database;
use App\Models\Usuario;
use App\Models\Entrada;
use PDO;
use DateTime;
use DateTimeZone;
use PDOException;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Entrada.php';

class UsuarioController {
    private $db;
    private $usuario;
    private $entrada;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuario = new Usuario($this->db);
        $this->entrada = new Entrada($this->db);
    }

    public function iniciar() {
        if (!empty($_POST)) {
            $this->usuario->correo = $_POST['correo'] ?? '';
            $clave = $_POST['clave'] ?? '';

            $query = "SELECT id_usuario, clave, rol FROM usuarios WHERE correo = :correo";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':correo', $this->usuario->correo);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($clave, $usuario['clave'])) {
                    $_SESSION['correo'] = $this->usuario->correo;
                    $_SESSION['id_usuario'] = $usuario['id_usuario'];
                    $_SESSION['rol'] = $usuario['rol'];

                    if ($usuario['rol'] === 'administrador') {
                        header("Location: /UR_CICLOPARQUEADERO/admin_inc");
                    } else {
                        header("Location: /UR_CICLOPARQUEADERO/inc_user");
                    }
                    exit;
                } else {
                    $_SESSION['error'] = 'Contraseña incorrecta.';
                }
            } else {
                $_SESSION['error'] = 'Correo no encontrado.';
            }
            header("Location: /UR_CICLOPARQUEADERO/");
            exit;
        }
        
    }
    

    public function mostrarUsuarioYEntradas() {
        if (!isset($_SESSION['correo'])) {
            $_SESSION['error'] = 'Debe iniciar sesión para ver esta página.';
            header("Location: /UR_CICLOPARQUEADERO/");
            exit;
        }

        $query = "
            SELECT e.id_entrada, e.fecha_hora, p.sede_parqueadero
            FROM entrada e
            JOIN parqueadero p ON e.id_parqueadero = p.id_parqueadero
            WHERE e.id_usuario = :id_usuario
            ORDER BY e.fecha_hora DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_usuario', $_SESSION['id_usuario']);
        $stmt->execute();

        $entradas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($entradas as &$entrada) {
            $date = new DateTime($entrada['fecha_hora'], new DateTimeZone('UTC'));
            $date->setTimezone(new DateTimeZone('America/Bogota'));
            $entrada['fecha_hora'] = $date->format('Y-m-d H:i:s');
        }

        $usuario = ['correo' => $_SESSION['correo']];
        return ['usuario' => $usuario, 'entradas' => $entradas];
    }

    public function obtenerTodosLosUsuarios() {
        $query = "SELECT * FROM usuarios";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerUsuariosConMasEntradas() {
        $query = "
            SELECT u.id_usuario, u.nombres, u.apellidos, u.correo, COUNT(e.id_entrada) as num_entradas
            FROM usuarios u
            JOIN entrada e ON u.id_usuario = e.id_usuario
            GROUP BY u.id_usuario
            ORDER BY num_entradas DESC
            LIMIT 5
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

  

    public function actualizarUsuario() {
        if (!empty($_POST) && isset($_POST['id_usuario'])) {
            $this->usuario->id_usuario = $_POST['id_usuario'];
            $this->usuario->nombres = $_POST['nombres'];
            $this->usuario->apellidos = $_POST['apellidos'];
            $this->usuario->correo = $_POST['correo'];
            $this->usuario->celular = $_POST['celular'];
            $this->usuario->rol = $_POST['rol'];

            if ($this->usuario->actualizar($this->usuario->id_usuario)) {
                $_SESSION['mensaje'] = 'Usuario actualizado correctamente.';
                header("Location: /UR_CICLOPARQUEADERO/admin_inc?success=1");
            } else {
                $_SESSION['error'] = 'Error al actualizar el usuario.';
                header("Location: /UR_CICLOPARQUEADERO/admin_inc?success=0");
            }
            exit;
        }
    }


    public function obtenerUsuarioPorId($id_usuario) {
        $query = "SELECT * FROM usuarios WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

   

    public function buscarUsuarios($termino) {
        $query = "SELECT * FROM usuarios WHERE nombres LIKE :termino OR apellidos LIKE :termino OR correo LIKE :termino";
        $stmt = $this->db->prepare($query);
        $termino = '%' . $termino . '%';
        $stmt->bindParam(':termino', $termino);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}



if (isset($_POST['iniciar'])) {
    $usuarioController = new UsuarioController();
    $usuarioController->iniciar();
}

if (isset($_POST['guardar_usuario'])) {
    $usuarioController = new UsuarioController();
    $usuarioController->actualizarUsuario();
}


