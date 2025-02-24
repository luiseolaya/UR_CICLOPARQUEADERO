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
        try {
            $query = "INSERT INTO usuarios (nombres, apellidos, correo, celular, clave) VALUES (:nombres, :apellidos, :correo, :celular, :clave)";
            $stmt = $this->conn->prepare($query);

            // Bind parameters
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
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Código de error para entrada duplicada
                throw new PDOException('El correo electrónico ya está registrado.', 23000);
            } else {
                throw $e; // Re-lanzar la excepción si no es un error de duplicado
            }
        }
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
