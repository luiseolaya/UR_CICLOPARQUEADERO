<?php

namespace App\Models;

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
                  VALUES (?, ?, CONVERT(datetime, ?, 120), ?, ?)";
    
        
        $fechaHora = new \DateTime($this->fecha_hora);
        $fechaHoraFormateada = $fechaHora->format('Y-m-d H:i:s');
    
        $params = [
            $this->id_usuario,
            $this->id_parqueadero,
            $fechaHoraFormateada, 
            $this->foto,
            $this->observaciones
        ];
    
      
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/cicloparqueaderos/phplogs.txt", "Consulta SQL: $query\n", FILE_APPEND);
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/cicloparqueaderos/phplogs.txt", "Parámetros: " . print_r($params, true) . "\n", FILE_APPEND);
    
        $stmt = sqlsrv_prepare($this->conn, $query, $params);
    
        if (!$stmt) {
            $errors = print_r(sqlsrv_errors(), true);
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/cicloparqueaderos/phplogs.txt", "ERROR en SQLSRV Prepare: $errors\n", FILE_APPEND);
            return false;
        }
    
        if (!sqlsrv_execute($stmt)) {
            $errors = print_r(sqlsrv_errors(), true);
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/cicloparqueaderos/phplogs.txt", "ERROR en SQLSRV Execute: $errors\n", FILE_APPEND);
            return false;
        }
    
        return true;
    }
}