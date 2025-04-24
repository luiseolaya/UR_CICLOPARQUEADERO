<?php

namespace App\Controllers;

use App\Config\Database;
use App\Models\Usuario;
use App\Models\Entrada;
use DateTime;
use DateTimeZone;

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
        if (empty($_POST)) {
            header("Location: /UR_CICLOPARQUEADERO/");
            exit;
        }

        $correo = $_POST['correo'] ?? '';
        $clave = $_POST['clave'] ?? '';

        $this->ldap_validation($correo, $clave);

        if (!isset($_SESSION['id_usuario'])) {
            $_SESSION['error'] = 'ERROR CON SUS CREDENCIALES';
            header("Location: /UR_CICLOPARQUEADERO/");
            exit;
        }

        $usuario = $this->usuario->obtenerPorCorreo($_SESSION['correo']);

        if (!$usuario) {
            $this->usuario->Ndocumento = $_SESSION['id_usuario'];
            $this->usuario->nombres = $_SESSION['nombres'];
            $this->usuario->apellidos = $_SESSION['apellidos'];
            $this->usuario->facultad = $_SESSION['facultad'];
            $this->usuario->correo = $_SESSION['correo'];
            $this->usuario->rol = $_SESSION['rol'];
            $this->usuario->terminos_condiciones = 0;

            $this->usuario->insertarUsuario();
            header("Location: /UR_CICLOPARQUEADERO/TERMINOS");
            exit;
        }

        if ($usuario['terminos_condiciones'] == 0) {
            header("Location: /UR_CICLOPARQUEADERO/TERMINOS");
        } else {
            $redirect = ($usuario['rol'] === 'administrador') ? '/ADMINISTRADOR' : '/inicio';
            header("Location: /UR_CICLOPARQUEADERO" . $redirect);
        }
        exit;
    }
    
    protected function ldap_validation($usuario, $clave)
    {
        if ($usuario == '' || $clave == '') {
            $_SESSION['error'] = "Usuario o clave nula";
            return;
        }

        $ldap_conect = ldap_connect("ldap://urosario.edu");
        if (!$ldap_conect) {
            $_SESSION['error'] = "ERROR DE CREDENCIAL";
            return;
        }

        $comunidad = "Universidad del Rosario";
        ldap_bind($ldap_conect, "Ldap_aplicaciones@urosario.edu", "fOC350u4nyvW5sqBRuwr") or die("Couldn't bind to AD!");
        $sr = ldap_search($ldap_conect, "OU=" . $comunidad . ",DC=urosario,DC=edu", "(&(objectClass=user)(userPrincipalName=" . $usuario . "))");

        $ldap_info = ldap_get_entries($ldap_conect, $sr);

        if ($ldap_info['count'] == 0) {
            $_SESSION['error'] = "Error información no encontrada";
            return;
        }

        $result = @ldap_bind($ldap_conect, $usuario, $clave);
        if (!$result) {
            $_SESSION['error'] = "Error en conexion con usuario y clave";
            return;
        }

        $nombres = $ldap_info[0]["givenname"][0];
        $apellidos = $ldap_info[0]["sn"][0];
        $cedula = $ldap_info[0]["postofficebox"][0];
        $mail = $ldap_info[0]["mail"][0];
        $facultad = iconv('ISO-8859-1', 'UTF-8', $ldap_info[0]["department"][0]);

        $usuarioExistente = $this->usuario->obtenerPorCorreo($mail);

        if (!$usuarioExistente) {
            $this->usuario->Ndocumento = $cedula;
            $this->usuario->nombres = strtoupper($nombres);
            $this->usuario->apellidos = strtoupper($apellidos);
            $this->usuario->facultad = strtoupper($facultad);
            $this->usuario->correo = $mail;
            $this->usuario->rol = "usuario";
            $this->usuario->terminos_condiciones = 0;

            $this->usuario->insertarUsuario();

            $usuarioExistente = $this->usuario->obtenerPorCorreo($mail);

        }

        $_SESSION['id_usuario'] = $usuarioExistente['id_usuario'];
        $_SESSION['rol'] = $usuarioExistente['rol'];
        $_SESSION['nombres'] = strtoupper($nombres);
        $_SESSION['apellidos'] = strtoupper($apellidos);
        $_SESSION['correo'] = $mail;
        $_SESSION['facultad'] = $facultad;
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
            WHERE e.id_usuario = ?
            ORDER BY e.fecha_hora DESC
        ";

        $params = [$_SESSION['id_usuario']];
        $stmt = sqlsrv_prepare($this->db, $query, $params);

        if (!$stmt) {
            die(print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_execute($stmt)) {
            $entradas = [];
            while ($entrada = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                // Verifica si $entrada['fecha_hora'] ya es una instancia de DateTime
                if (!($entrada['fecha_hora'] instanceof DateTime)) {
                    $entrada['fecha_hora'] = new DateTime($entrada['fecha_hora'], new DateTimeZone('UTC'));
                }
                // Cambia la zona horaria
                $entrada['fecha_hora']->setTimezone(new DateTimeZone('America/Bogota'));
                $entrada['fecha_hora'] = $entrada['fecha_hora']->format('Y-m-d H:i:s');
                $entradas[] = $entrada;
            }
        } else {
            die(print_r(sqlsrv_errors(), true));
        }
        

        $usuario = ['correo' => $_SESSION['correo']];
        return ['usuario' => $usuario, 'entradas' => $entradas];
    }

    public function obtenerTodosLosUsuarios() {
        $query = "SELECT * FROM usuarios";
        $stmt = sqlsrv_prepare($this->db, $query);

        if (!$stmt) {
            die(print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_execute($stmt)) {
            $usuarios = [];
            while ($usuario = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $usuarios[] = $usuario;
            }
            return $usuarios;
        } else {
            die(print_r(sqlsrv_errors(), true));
        }
    }

    public function obtenerUsuariosConMasEntradas() {
        $query = "
            SELECT TOP 5
                u.id_usuario, 
                u.nombres, 
                u.apellidos, 
                u.correo, 
                COUNT(DISTINCT CONVERT(DATE, e.fecha_hora)) as num_entradas, 
                COUNT(e.id_entrada) as total_entradas
            FROM usuarios u
            INNER JOIN entrada e ON u.id_usuario = e.id_usuario
            GROUP BY u.id_usuario, u.nombres, u.apellidos, u.correo
            ORDER BY num_entradas DESC
        ";
    
        $stmt = sqlsrv_prepare($this->db, $query);
    
        if (!$stmt) {
            die(print_r(sqlsrv_errors(), true));
        }
    
        if (sqlsrv_execute($stmt)) {
            $usuarios = [];
            while ($usuario = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $usuarios[] = $usuario;
            }
            return $usuarios;
        } else {
            die(print_r(sqlsrv_errors(), true));
        }
    }


    public function obtenerUsuariosConTotalEntradas() {
        $query = "
            SELECT TOP 5
                u.id_usuario, 
                u.nombres, 
                u.apellidos, 
                u.correo, 
                COUNT(e.id_entrada) as total_entradas
            FROM usuarios u
            LEFT JOIN entrada e ON u.id_usuario = e.id_usuario
            GROUP BY u.id_usuario, u.nombres, u.apellidos, u.correo
            ORDER BY total_entradas DESC
        ";
    
        $stmt = sqlsrv_prepare($this->db, $query);
    
        if (!$stmt) {
            die(print_r(sqlsrv_errors(), true));
        }
    
        if (sqlsrv_execute($stmt)) {
            $usuarios = [];
            while ($usuario = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $usuarios[] = $usuario;
            }
            return $usuarios;
        } else {
            die(print_r(sqlsrv_errors(), true));
        }
    }
    public function actualizarUsuario() {
        if (!empty($_POST) && isset($_POST['id_usuario'])) {
            $query = "UPDATE usuarios SET celular = ?, rol = ? WHERE id_usuario = ?";
            $params = [$_POST['celular'], $_POST['rol'], $_POST['id_usuario']];
            $stmt = sqlsrv_prepare($this->db, $query, $params);

            if (!$stmt) {
                die(print_r(sqlsrv_errors(), true));
            }

            if (sqlsrv_execute($stmt)) {
                $_SESSION['mensaje'] = 'Usuario actualizado correctamente.';
                header("Location: /UR_CICLOPARQUEADERO/ADMINISTRADOR");
            } else {
                $_SESSION['error'] = 'Error al actualizar el usuario.';
                header("Location: /UR_CICLOPARQUEADERO/ADMINISTRADOR");
            }
            exit;
        }
    }

    public function mostrarActualizarTelefono() {
        if (!isset($_SESSION['id_usuario'])) {
            $_SESSION['error'] = 'Debe iniciar sesión para actualizar su teléfono.';
            header("Location: /UR_CICLOPARQUEADERO/");
            exit;
        }
    
        $usuario = $this->usuario->obtenerPorId($_SESSION['id_usuario']);
        if (!$usuario) {
            $_SESSION['error'] = 'Usuario no encontrado.';
            header("Location: /UR_CICLOPARQUEADERO/");
            exit;
        }
    
        return $usuario;
    }

    public function actualizarTerminosYTelefono() {
        if (!empty($_POST) && isset($_POST['celular']) && isset($_POST['terminos'])) {
            $this->usuario->id_usuario = $_SESSION['id_usuario'];
            $this->usuario->celular = $_POST['celular'];
            $this->usuario->terminos_condiciones = 1;

            if ($this->usuario->actualizarCelularYTerminos($this->usuario->id_usuario)) {
                $_SESSION['mensaje'] = 'Teléfono y términos actualizados correctamente.';
                header("Location: /UR_CICLOPARQUEADERO/inicio");
            } else {
                $_SESSION['error'] = 'Error al actualizar la información.';
                header("Location: /UR_CICLOPARQUEADERO/TERMINOS");
            }
            exit;
        } else {
            $_SESSION['error'] = 'Datos incompletos. Por favor, intente nuevamente.';
            header("Location: /UR_CICLOPARQUEADERO/TERMINOS");
            exit;
        }
    }
   
    public function actualizar() {
        if (!empty($_POST) && isset($_POST['celular'])) {
            $query = "UPDATE usuarios SET celular = ? WHERE id_usuario = ?";
            $params = [$_POST['celular'], $_SESSION['id_usuario']];
            $stmt = sqlsrv_prepare($this->db, $query, $params);

            if (!$stmt) {
                die(print_r(sqlsrv_errors(), true));
            }

            if (sqlsrv_execute($stmt)) {
                $_SESSION['mensaje'] = 'Teléfono actualizado correctamente.';
                header("Location: /UR_CICLOPARQUEADERO/inicio");
            } else {
                $_SESSION['error'] = 'Error al actualizar teléfono.';
                header("Location: /UR_CICLOPARQUEADERO/actualizar_telefono");
            }
            exit;
        } else {
            $_SESSION['error'] = 'Datos incompletos. Por favor, intente nuevamente.';
            header("Location: /UR_CICLOPARQUEADERO/actualizar_telefono");
            exit;
        }
    } 

    public function obtenerUsuarioPorId($id_usuario) {
        $query = "SELECT * FROM usuarios WHERE id_usuario = ?";
        $stmt = sqlsrv_prepare($this->db, $query, [$id_usuario]);

        if (!$stmt) {
            die(print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_execute($stmt)) {
            return sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        } else {
            die(print_r(sqlsrv_errors(), true));
        }
    }

    public function buscarUsuarios($termino) {
        $query = "SELECT * FROM usuarios WHERE nombres LIKE ? OR apellidos LIKE ? OR correo LIKE ?";
        $termino = '%' . $termino . '%';
        $params = [$termino, $termino, $termino];
        $stmt = sqlsrv_prepare($this->db, $query, $params);

        if (!$stmt) {
            die(print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_execute($stmt)) {
            $usuarios = [];
            while ($usuario = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $usuarios[] = $usuario;
            }
            return $usuarios;
        } else {
            die(print_r(sqlsrv_errors(), true));
        }
    }
}

$usuarioController = new UsuarioController();

if (isset($_POST['iniciar'])) {
    $usuarioController->iniciar();
}

if (isset($_POST['guardar_usuario'])) {
    $usuarioController->actualizarUsuario();
}
if (isset($_POST['actualizar_telefono'])) {
    $usuarioController->actualizarTerminosYTelefono();
}
if (isset($_POST['actualizar'])) {
    $usuarioController->actualizar();
}