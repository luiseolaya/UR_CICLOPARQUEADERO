<?php

namespace App\Models;

use PDO;
use PDOException;

class ModeloEvidencia {
    private $conn;
    private $table_name = "evidencia"; 

    public $id_usuario;
    public $foto; // Correct property name

    public function __construct($db) {
        $this->conn = $db;
    }

    public function crearEvidencia() {
        $query = "INSERT INTO " . $this->table_name . " SET id_usuario=:id_usuario, foto=:foto, fecha_hora=NOW()";

        $stmt = $this->conn->prepare($query);

        // Limpieza de datos
        $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));
        $this->foto = $this->foto; // No limpiar la imagen

        // Bind de cada valor
        $stmt->bindParam(':id_usuario', $this->id_usuario);
        $stmt->bindParam(':foto', $this->foto, PDO::PARAM_LOB);

        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        return false;
    }

    public function obtenerEvidenciasPorUsuario($id_usuario) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}