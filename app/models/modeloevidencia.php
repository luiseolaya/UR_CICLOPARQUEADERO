<?php

namespace App\Models;

use PDO;
use PDOException;

class ModeloEvidencia {
    private $conn;
    private $table_name = "evidencia"; 

    public $id_usuario;
    public $evidencia;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function crearEvidencia() {
        $query = "INSERT INTO " . $this->table_name . " SET id_usuario=:id_usuario, foto=:evidencia, fecha_hora=NOW()";

        $stmt = $this->conn->prepare($query);

        // Limpieza de datos
        $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));
        $this->evidencia = $this->evidencia; // No limpiar la imagen

        // Bind de cada valor
        $stmt->bindParam(':id_usuario', $this->id_usuario);
        $stmt->bindParam(':evidencia', $this->evidencia, PDO::PARAM_LOB);

        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        return false;
    }
}