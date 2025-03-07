<?php
namespace App\Controllers;

require_once __DIR__ . '/../../vendor/fpdf182/fpdf.php';

use FPDF;
use app\Config\Database;
use PDO;

class ReporteController {
    public function generarReporte() {
        // Desactiva la salida de errores temporalmente
        ob_start();

        // Crea el PDF
        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Body();
        
        // Limpia el buffer de salida y desactiva la salida de errores
        ob_end_clean();

        // Envía el PDF al navegador
        $pdf->Output('I', 'Documento Final.pdf');
        exit; // Asegúrate de que no haya ninguna salida adicional
    }
}

class PDF extends FPDF {
    function Header() {
        $this->SetFont("Arial", "", 24);
        $this->Image($_SERVER['DOCUMENT_ROOT'] . "/UR_CICLOPARQUEADERO/public/img/LOGOU.png", 10, 10, 20);
        $this->Cell(30);
        $this->Cell(0, 10, utf8_decode("Usuarios y Entradas"), 0, 1, 'C');
        $this->Ln(10);
    }

    function Body() {
        $database = new Database();
        $my = $database->getConnection();
        $sql = "SELECT u.id_usuario, u.nombres, u.correo, u.rol, COUNT(e.id_entrada) as entradas
                FROM usuarios u
                LEFT JOIN entrada e ON u.id_usuario = e.id_usuario
                GROUP BY u.id_usuario, u.nombres, u.correo, u.rol
                ORDER BY entradas DESC
                LIMIT 5";
        $stm = $my->prepare($sql);
        if (!$stm) {
            die("Error en la preparación de la consulta: " . $my->errorInfo()[2]);
        }
        $stm->execute();
        $stm->bindColumn('id_usuario', $id_usuario);
        $stm->bindColumn('nombres', $nombres);
        $stm->bindColumn('correo', $correo);
        $stm->bindColumn('rol', $rol);
        $stm->bindColumn('entradas', $entradas);
        $hay = $stm->rowCount();
        if ($hay == 0) {
            $this->Cell(0, 10, "No hay registros que mostrar", 1, 1, 'C');
        } else {
            $this->SetFont("Arial", 'B', 12);
            $this->SetTextColor(62, 72, 204);
            $this->Cell(20, 10, "ID", 1, 0, 'C');
            $this->Cell(50, 10, "Nombres", 1, 0, 'C');
            $this->Cell(60, 10, "Correo", 1, 0, 'C');
            $this->Cell(30, 10, "Rol", 1, 0, 'C');
            $this->Cell(30, 10, "Entradas", 1, 1, 'C');
            $this->SetFont("Arial", '', 12);
            $this->SetTextColor(0, 0, 0);
            while ($stm->fetch(PDO::FETCH_BOUND)) {
                $nombres = utf8_decode($nombres);
                $this->Cell(20, 10, $id_usuario, 1, 0, 'C');
                $this->Cell(50, 10, $nombres, 1, 0, 'C');
                $this->Cell(60, 10, $correo, 1, 0, 'C');
                $this->Cell(30, 10, $rol, 1, 0, 'C');
                $this->Cell(30, 10, $entradas, 1, 1, 'C');
            }
        }
        $stm->closeCursor();
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont("Arial", 'I', 10);
        $this->Cell(0, 10, "Como generar archivos PDF con PHP", 0, 0, 'C');
    }
}