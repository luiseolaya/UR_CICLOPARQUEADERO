<?php
namespace App\Controllers;

require_once __DIR__ . '/../../vendor/fpdf182/fpdf.php';

use FPDF;
use app\Config\Database;
use PDO;

class ReporteController {
    public function generarReporte() {
        ob_start(); 
        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 10); 
        $pdf->Body();
        ob_end_clean();
        $pdf->Output('I', 'Entradas Cicloparqueadero.pdf');
        exit;
    }
}

class PDF extends FPDF {
    function Header() {
        $this->Watermark();
        $this->SetFont("Arial", "", 18);
        $this->Image($_SERVER['DOCUMENT_ROOT'] . "/UR_CICLOPARQUEADERO/public/img/LOGOU.png", 10, 6, 20);
        $this->Ln(5);
        $this->Cell(30);
        $this->Cell(0, 10, utf8_decode("Reporte de Entradas"), 0, 1, 'C');
        $this->Ln(5);
    }

    function Watermark() {
        
        $watermarkPath = $_SERVER['DOCUMENT_ROOT'] . "/UR_CICLOPARQUEADERO/public/img/Marca_Agua.png";
        
        list($width, $height) = getimagesize($watermarkPath);
        
        $x = ($this->GetPageWidth() - $width / 4) / 2;
        $y = ($this->GetPageHeight() - $height / 13) / 8;
        $this->Image($watermarkPath, $x, $y, $width / 4, $height / 4);
    }

    function Body() {
        $database = new Database();
        $my = $database->getConnection();
        $sql = "SELECT u.Ndocumento, u.nombres, u.apellidos, u.correo, u.rol, COUNT(e.id_entrada) as entradas
                FROM usuarios u
                LEFT JOIN entrada e ON u.id_usuario = e.id_usuario
                GROUP BY u.Ndocumento, u.nombres, u.apellidos, u.correo, u.rol
                ORDER BY entradas DESC
                LIMIT 5";
        $stm = $my->prepare($sql);
        if (!$stm) {
            die("Error en la preparación de la consulta: " . $my->errorInfo()[2]);
        }
        $stm->execute();
        $stm->bindColumn('Ndocumento', $Ndocumento);
        $stm->bindColumn('nombres', $nombres);
        $stm->bindColumn('apellidos', $apellidos);
        $stm->bindColumn('correo', $correo);
        $stm->bindColumn('entradas', $entradas);
        //campo de encabezado
        $this->SetFont("Arial", 'B', 9);
        $this->SetFillColor(200, 220, 255); 
        $this->Cell(25, 8, "N.Documento", 1, 0, 'C', true);
        $this->Cell(40, 8, "Nombres", 1, 0, 'C', true);
        $this->Cell(40, 8, "Apellidos", 1, 0, 'C', true);
        $this->Cell(50, 8, "Correo", 1, 0, 'C', true);
        $this->Cell(20, 8, "N.Entradas", 1, 1, 'C', true);

        $this->SetFont("Arial", '', 9);
        while ($stm->fetch(PDO::FETCH_BOUND)) {
            $nombres = utf8_decode($nombres);
            $apellidos = utf8_decode($apellidos);
            $correo = utf8_decode($correo);

            $this->Cell(25, 8, $Ndocumento, 1, 0, 'C');
            $this->Cell(40, 8, substr($nombres, 0, 20), 1, 0, 'C'); 
            $this->Cell(40, 8, substr($apellidos, 0, 20), 1, 0, 'C');
            $this->Cell(50, 8, substr($correo, 0, 30), 1, 0, 'C');
            $this->Cell(20, 8, $entradas, 1, 1, 'C');
        }
        $stm->closeCursor();
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont("Arial", 'I', 8);
        $this->Cell(0, 10, "Universidad Del Rosario", 0, 0, 'C');
    }
}