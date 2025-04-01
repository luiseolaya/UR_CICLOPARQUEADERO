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
    public $observaciones;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function crearEntrada() {
        $query = "INSERT INTO entrada (id_usuario, id_parqueadero, fecha_hora, foto, observaciones) 
                  VALUES (:id_usuario, :id_parqueadero, :fecha_hora, :foto, :observaciones)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_usuario', $this->id_usuario);
        $stmt->bindParam(':id_parqueadero', $this->id_parqueadero);
        $stmt->bindParam(':fecha_hora', $this->fecha_hora);
        $stmt->bindParam(':foto', $this->foto);
        $stmt->bindParam(':observaciones', $this->observaciones);

        return $stmt->execute();
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
