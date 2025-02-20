<?php

namespace App\Models;

use PDO;
use PDOException;

error_log('Incluyendo usuario.php');

class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id_usuario;
    public $nombres;
    public $apellidos;
    public $correo;
    public $celular;
    public $clave;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function crear() {
        $query = "INSERT INTO " . $this->table_name . " SET nombres=:nombres, apellidos=:apellidos, correo=:correo, celular=:celular, clave=:clave";
        $stmt = $this->conn->prepare($query);

        $this->nombres = htmlspecialchars(strip_tags($this->nombres));
        $this->apellidos = htmlspecialchars(strip_tags($this->apellidos));
        $this->correo = htmlspecialchars(strip_tags($this->correo));
        $this->celular = htmlspecialchars(strip_tags($this->celular));
        $this->clave = htmlspecialchars(strip_tags($this->clave));

        $stmt->bindParam(':nombres', $this->nombres);
        $stmt->bindParam(':apellidos', $this->apellidos);
        $stmt->bindParam(':correo', $this->correo);
        $stmt->bindParam(':celular', $this->celular);
        $stmt->bindParam(':clave', $this->clave);

        if ($stmt->execute()) {
            $this->id_usuario = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    public function validar() {
        $query = "SELECT id_usuario, clave FROM " . $this->table_name . " WHERE correo = :correo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':correo', $this->correo);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return false;
    }
}
