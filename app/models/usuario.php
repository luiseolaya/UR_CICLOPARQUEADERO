<?php

namespace App\Models;

use PDO;
use PDOException;


class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id_usuario;
    public $nombres;
    public $apellidos;
    public $correo;
    public $celular;
    public $clave;
    public $rol; 

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
        $query = "UPDATE usuarios SET nombres = :nombres, apellidos = :apellidos, correo = :correo, celular = :celular, rol = :rol WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':id_usuario', $id);
        $stmt->bindParam(':nombres', $this->nombres);
        $stmt->bindParam(':apellidos', $this->apellidos);
        $stmt->bindParam(':correo', $this->correo);
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

  
}
