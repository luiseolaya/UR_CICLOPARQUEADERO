<?php

namespace App\Models;

use PDO;
use PDOException;

class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id_usuario;
    public $Ndocumento;
    public $nombres;
    public $apellidos;
    public $correo;
    public $celular;
    public $rol;
    public $terminos_condiciones;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function validar() {
        $query = "SELECT id_usuario, clave, rol FROM " . $this->table_name . " WHERE correo = :correo"; // Seleccionar también el campo rol
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':correo', $this->correo);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
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
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizar($id) {
        $query = "UPDATE usuarios 
                  SET celular = :celular, rol = :rol 
                  WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);

        // Bind 
        $stmt->bindParam(':id_usuario', $id);
        $stmt->bindParam(':celular', $this->celular);
        $stmt->bindParam(':rol', $this->rol);

        return $stmt->execute();
    }

    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertarUsuario() {
        $query = "INSERT INTO " . $this->table_name . " (Ndocumento, nombres, apellidos, correo, rol, terminos_condiciones)
                  VALUES (:Ndocumento, :nombres, :apellidos, :correo, :rol, :terminos_condiciones)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':Ndocumento', $this->Ndocumento);
        $stmt->bindParam(':nombres', $this->nombres);
        $stmt->bindParam(':apellidos', $this->apellidos);
        $stmt->bindParam(':correo', $this->correo);
        $stmt->bindParam(':rol', $this->rol);
        $stmt->bindParam(':terminos_condiciones', $this->terminos_condiciones);

        return $stmt->execute();
    }

    public function obtenerPorCorreo($correo) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE correo = :correo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarCelularYTerminos($id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET celular = :celular, terminos_condiciones = :terminos_condiciones 
                  WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);

        // Asegurarse de que los valores no sean nulos
        $this->celular = $this->celular ?? '';
        $this->terminos_condiciones = $this->terminos_condiciones ?? 0;

        $stmt->bindParam(':id_usuario', $id);
        $stmt->bindParam(':celular', $this->celular);
        $stmt->bindParam(':terminos_condiciones', $this->terminos_condiciones);

        return $stmt->execute();
    }
}
