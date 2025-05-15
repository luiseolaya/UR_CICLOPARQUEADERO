<?php
namespace App\Controllers;

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use app\Config\Database;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

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
// Cambiar el nombre de la primera hoja a "Entradas Únicas"
        $sheet->setTitle("Entradas Únicas");
        
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath($_SERVER['DOCUMENT_ROOT'] . '/cicloparqueaderos/public/img/LOGOU.png');
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
              ->setCellValue('F5', 'Total Entradas');

       
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 15],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFCCE5FF']
            ]
        ];
        $sheet->getStyle('A5:F5')->applyFromArray($headerStyle);

        $rowIndex = 6;
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $sheet->setCellValue('A' . $rowIndex, $row['Ndocumento'])
                  ->setCellValue('B' . $rowIndex, $row['nombres'])
                  ->setCellValue('C' . $rowIndex, $row['apellidos'])
                  ->setCellValue('D' . $rowIndex, $row['correo'])
                  ->setCellValue('E' . $rowIndex, $row['entradas_unicas'])
                  ->setCellValue('F' . $rowIndex, $row['total_entradas']);
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
//Hoja 2
$sql2 = "
            SELECT u.nombres,
                   u.apellidos,
                   u.facultad,
                   u.Ndocumento,
                   u.celular,
                   u.correo,
                   e.fecha_hora,
                   e.observaciones, 
                   p.sede_parqueadero
            FROM [dbo].[entrada] e
            LEFT JOIN parqueadero p ON e.id_parqueadero = p.id_parqueadero
            LEFT JOIN usuarios u ON u.id_usuario = e.id_usuario
        ";

        $stmt2 = sqlsrv_query($connection, $sql2);
        if ($stmt2 === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        // Crear nueva hoja para la segunda consulta
        $sheet2 = new Worksheet($spreadsheet, 'Detalle de Entradas');
        $spreadsheet->addSheet($sheet2);

        // Encabezados de la segunda hoja
        $headers2 = [
            'A1' => 'Nombres',
            'B1' => 'Apellidos',
            'C1' => 'Facultad',
            'D1' => 'N° Documento',
            'E1' => 'Celular',
            'F1' => 'Correo',
            'G1' => 'Fecha/Hora',
            'H1' => 'Observaciones',
            'I1' => 'Sede Parqueadero'
        ];

        foreach ($headers2 as $cell => $text) {
            $sheet2->setCellValue($cell, $text);
        }

        // Llenar la segunda hoja con los datos
        $rowIndex = 2;
        while ($row = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
            $sheet2->setCellValue('A' . $rowIndex, $row['nombres'])
                   ->setCellValue('B' . $rowIndex, $row['apellidos'])
                   ->setCellValue('C' . $rowIndex, $row['facultad'])
                   ->setCellValue('D' . $rowIndex, $row['Ndocumento'])
                   ->setCellValue('E' . $rowIndex, $row['celular'])
                   ->setCellValue('F' . $rowIndex, $row['correo'])
                   ->setCellValue('G' . $rowIndex, $row['fecha_hora']->format('Y-m-d H:i:s'))
                   ->setCellValue('H' . $rowIndex, $row['observaciones'])
                   ->setCellValue('I' . $rowIndex, $row['sede_parqueadero']);
            $rowIndex++;
        }

        // Ajustar el tamaño de las columnas en la segunda hoja
        foreach (range('A', 'I') as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }
    
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Reporte_Cicloparqueaderos.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}