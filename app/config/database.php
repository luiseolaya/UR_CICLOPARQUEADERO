<?php

namespace App\Config;

use PDO;
use PDOException;

class Database {
    private $host = 'CNF80449\SQLEXPRESS';
    private $db_name = 'db_cicloparqueadero';
    private $username = 'user_cicloparqueadero';
    private $password = 'Prueb4*2025';
    public $conn;

    public function getConnection_old() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }

    public function getConnection(){
        $serverName = $this->host;
        $connectionInfo = array(
            "Database" => $this->db_name,
			"UID" => $this->username,
			"PWD" => $this->password,
			//"ColumnEncryption" => "enabled",
			//"TrustServerCertificate" => 1,
			"CharacterSet" => "UTF-8"
        );
		$this->conn = sqlsrv_connect($serverName, $connectionInfo);
        if (!$this->conn) {
			echo "Conexión no se pudo establecer.<br />";
			die(print_r(sqlsrv_errors(), true));
		}
        return $this->conn;


    }
}
?>