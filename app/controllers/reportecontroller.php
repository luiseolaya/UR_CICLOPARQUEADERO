<?php
namespace App\Controllers;

require_once __DIR__ . '/../../vendor/fpdf182/fpdf.php';

use FPDF;
use app\Config\Database;
use PDO;

class ReporteController {
    public function generarReporte() {
        ob_start(); // Evita errores de salida
        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 10); // Fuente más pequeña
        $pdf->Body();
        ob_end_clean();
        $pdf->Output('I', 'Documento Final.pdf');
        exit;
    }
}

class PDF extends FPDF {
    function Header() {
        $this->SetFont("Arial", "", 18);
        $this->Image($_SERVER['DOCUMENT_ROOT'] . "/UR_CICLOPARQUEADERO/public/img/LOGOU.png", 10, 6, 20);
        $this->Ln(5);
        $this->Cell(30);
        $this->Cell(0, 10, utf8_decode("Reporte de Usuarios"), 0, 1, 'C');
        $this->Ln(5);
    }

    function Body() {
        $database = new Database();
        $my = $database->getConnection();
        $sql = "SELECT u.id_usuario, u.nombres, u.apellidos, u.correo, u.rol, COUNT(e.id_entrada) as entradas
                FROM usuarios u
                LEFT JOIN entrada e ON u.id_usuario = e.id_usuario
                GROUP BY u.id_usuario, u.nombres, u.apellidos, u.correo, u.rol
                ORDER BY entradas DESC
                LIMIT 999";
        $stm = $my->prepare($sql);
        if (!$stm) {
            die("Error en la preparación de la consulta: " . $my->errorInfo()[2]);
        }
        $stm->execute();
        $stm->bindColumn('id_usuario', $id_usuario);
        $stm->bindColumn('nombres', $nombres);
        $stm->bindColumn('apellidos', $apellidos);
        $stm->bindColumn('correo', $correo);
        $stm->bindColumn('rol', $rol);
        $stm->bindColumn('entradas', $entradas);

        $this->SetFont("Arial", 'B', 9);
        $this->SetFillColor(200, 220, 255); // Color de fondo
        $this->Cell(15, 8, "ID", 1, 0, 'C', true);
        $this->Cell(40, 8, "Nombres", 1, 0, 'C', true);
        $this->Cell(40, 8, "Apellidos", 1, 0, 'C', true);
        $this->Cell(50, 8, "Correo", 1, 0, 'C', true);
        $this->Cell(20, 8, "Rol", 1, 0, 'C', true);
        $this->Cell(20, 8, "Entradas", 1, 1, 'C', true);

        $this->SetFont("Arial", '', 9);
        while ($stm->fetch(PDO::FETCH_BOUND)) {
            $nombres = utf8_decode($nombres);
            $apellidos = utf8_decode($apellidos);
            $correo = utf8_decode($correo);

            $this->Cell(15, 8, $id_usuario, 1, 0, 'C');
            $this->Cell(40, 8, substr($nombres, 0, 20), 1, 0, 'C'); // Limita a 20 caracteres
            $this->Cell(40, 8, substr($apellidos, 0, 20), 1, 0, 'C');
            $this->Cell(50, 8, substr($correo, 0, 30), 1, 0, 'C');
            $this->Cell(20, 8, $rol, 1, 0, 'C');
            $this->Cell(20, 8, $entradas, 1, 1, 'C');
        }
        $stm->closeCursor();
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont("Arial", 'I', 8);
        $this->Cell(0, 10, "Reporte generado automaticamente", 0, 0, 'C');
    }
}


