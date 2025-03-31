<?php

namespace App\Models;

use PDO;
use PDOException;

class Entrada {
    private $conn;
    private $table_name = "entrada";

    public $id_usuario;
    public $id_parqueadero;
    public $fecha_hora;
    public $foto;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function crearEntrada() {
        try {
            // Verificar que el id_usuario no sea nulo
            if (empty($this->id_usuario)) {
                throw new PDOException('El id_usuario no puede ser nulo.');
            }

            $query = "INSERT INTO " . $this->table_name . " 
                      SET id_usuario = :id_usuario, id_parqueadero = :id_parqueadero, fecha_hora = :fecha_hora, foto = :foto";
            $stmt = $this->conn->prepare($query);

            $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));
            $this->id_parqueadero = htmlspecialchars(strip_tags($this->id_parqueadero));
            $this->fecha_hora = htmlspecialchars(strip_tags($this->fecha_hora));
            $this->foto = $this->foto; // No limpiar la imagen

            $stmt->bindParam(':id_usuario', $this->id_usuario);
            $stmt->bindParam(':id_parqueadero', $this->id_parqueadero);
            $stmt->bindParam(':fecha_hora', $this->fecha_hora);
            $stmt->bindParam(':foto', $this->foto, PDO::PARAM_LOB);

            if ($stmt->execute()) {
                return true;
            }

            return false;
        } catch (PDOException $e) {
            throw new PDOException('Error al crear la entrada en el modelo: ' . $e->getMessage(), (int)$e->getCode());
        }
    }
    /*
    public function existeEntradaHoy($id_usuario) {
        $query = "
            SELECT COUNT(*) as total
            FROM entrada
            WHERE id_usuario = :id_usuario
            AND DATE(fecha_hora) = CURDATE()
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }*/
}
