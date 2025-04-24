<?php
namespace App\Controllers;

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use app\Config\Database;

class ReporteController {
    public function generarReporte() {
        $database = new Database();
        $connection = $database->getConnection();

        $sql = "
            SELECT 
                u.Ndocumento, 
                u.nombres, 
                u.apellidos, 
                u.correo, 
                COUNT(DISTINCT CONVERT(DATE, e.fecha_hora)) AS entradas_unicas,
                COUNT(e.id_entrada) AS total_entradas,
                COUNT(e.observaciones) AS total_observaciones
            FROM usuarios u
            LEFT JOIN entrada e ON u.id_usuario = e.id_usuario
            GROUP BY u.Ndocumento, u.nombres, u.apellidos, u.correo
            ORDER BY entradas_unicas DESC
        ";

        $stmt = sqlsrv_query($connection, $sql);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        // Crear un nuevo archivo Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath($_SERVER['DOCUMENT_ROOT'] . '/UR_CICLOPARQUEADERO/public/img/LOGOU.png');
        $drawing->setHeight(87);
        $drawing->setCoordinates('c1');
        $drawing->setWorksheet($sheet); 

         
        $sheet->mergeCells('A3:G3');
        $sheet->setCellValue('A3', 'Reporte de Entradas - Cicloparqueadero');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 18],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ]);

        
        $sheet->setCellValue('A5', 'N.Documento')
              ->setCellValue('B5', 'Nombres')
              ->setCellValue('C5', 'Apellidos')
              ->setCellValue('D5', 'Correo')
              ->setCellValue('E5', 'Entradas Únicas')
              ->setCellValue('F5', 'Total Entradas')
              ->setCellValue('G5', 'Total Observaciones');

       
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 15],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFCCE5FF']
            ]
        ];
        $sheet->getStyle('A5:G5')->applyFromArray($headerStyle);

        $rowIndex = 6;
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $sheet->setCellValue('A' . $rowIndex, $row['Ndocumento'])
                  ->setCellValue('B' . $rowIndex, $row['nombres'])
                  ->setCellValue('C' . $rowIndex, $row['apellidos'])
                  ->setCellValue('D' . $rowIndex, $row['correo'])
                  ->setCellValue('E' . $rowIndex, $row['entradas_unicas'])
                  ->setCellValue('F' . $rowIndex, $row['total_entradas'])
                  ->setCellValue('G' . $rowIndex, $row['total_observaciones']);
            $rowIndex++;
        }

        $headerStyle = [
            'font' => [ 'size' => 12],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A5:G' . ($rowIndex - 1))->applyFromArray($headerStyle);


        
        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

    
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Reporte_Cicloparqueaderos.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}