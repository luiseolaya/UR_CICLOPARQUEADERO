<?php

namespace App\Models;

use PDOException;

class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id_usuario;
    public $Ndocumento;
    public $nombres;
    public $apellidos;
    public $facultad;
    public $correo;
    public $celular;
    public $rol;
    public $terminos_condiciones;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function validar() {
        $query = "SELECT id_usuario, clave, rol FROM " . $this->table_name . " WHERE correo = ?";
        $stmt = sqlsrv_prepare($this->conn, $query, [$this->correo]);

        if (!$stmt) {
            die(print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_execute($stmt)) {
            return sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        }

        return false;
    }

    public function obtenerUsuariosConEntradas() {
        $query = "
            SELECT u.id_usuario, u.nombres, u.apellidos, u.correo, COUNT(e.id_entrada) as num_entradas
            FROM usuarios u
            LEFT JOIN entrada e ON u.id_usuario = e.id_usuario
            GROUP BY u.id_usuario
        ";
        $stmt = sqlsrv_prepare($this->conn, $query);

        if (!$stmt) {
            die(print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_execute($stmt)) {
            $usuarios = [];
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $usuarios[] = $row;
            }
            return $usuarios;
        }

        return [];
    }

    public function actualizar($id) {
        $query = "UPDATE usuarios 
                  SET celular = ?, rol = ? 
                  WHERE id_usuario = ?";
        $stmt = sqlsrv_prepare($this->conn, $query, [$this->celular, $this->rol, $id]);

        if (!$stmt) {
            die(print_r(sqlsrv_errors(), true));
        }

        return sqlsrv_execute($stmt);
    }

    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_usuario = ?";
        $stmt = sqlsrv_prepare($this->conn, $query, [$id]);

        if (!$stmt) {
            die(print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_execute($stmt)) {
            return sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        }

        return false;
    }

    public function insertarUsuario() {
        $query = "INSERT INTO " . $this->table_name . " (Ndocumento, nombres, apellidos, facultad, correo, rol, terminos_condiciones)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $this->Ndocumento,
            $this->nombres,
            $this->apellidos,
            $this->facultad,
            $this->correo,
            $this->rol,
            $this->terminos_condiciones
        ];
        $stmt = sqlsrv_prepare($this->conn, $query, $params);

        if (!$stmt) {
            die(print_r(sqlsrv_errors(), true));
        }

        return sqlsrv_execute($stmt);
    }

    public function obtenerPorCorreo($correo) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE correo = ?";
        $stmt = sqlsrv_prepare($this->conn, $query, [$correo]);

        if (!$stmt) {
            die(print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_execute($stmt)) {
            return sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        }

        return false;
    }

    public function actualizarCelularYTerminos($id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET celular = ?, terminos_condiciones = ? 
                  WHERE id_usuario = ?";
        $params = [$this->celular, $this->terminos_condiciones, $id];
        $stmt = sqlsrv_prepare($this->conn, $query, $params);

        if (!$stmt) {
            die(print_r(sqlsrv_errors(), true));
        }

        return sqlsrv_execute($stmt);
    }
}
