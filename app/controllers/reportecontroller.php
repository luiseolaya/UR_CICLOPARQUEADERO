<?php
namespace App\Controllers;

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

        
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=Reporte_Cicloparqueaderos.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

       
        echo '<html>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo '<table width="100%" border="1">';
        echo '<tr align="center">';
        echo '<td colspan="6"><b>REPORTE DE ENTRADAS POR USUARIO - CICLOPARQUEADERO</b></td>';
        echo '</tr>';
        echo '<tr align="center">';
        echo '<td><b>N.Documento</b></td>';
        echo '<td><b>Nombres</b></td>';
        echo '<td><b>Apellidos</b></td>';
        echo '<td><b>Correo</b></td>';
        echo '<td><b>Entradas Únicas</b></td>';
        echo '<td><b>Total Entradas</b></td>';
        echo '<td><b>Total Observaciones</b></td>';
        echo '</tr>';

        // Fetch and display data
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['Ndocumento']) . '</td>';
            echo '<td>' . htmlspecialchars($row['nombres']) . '</td>';
            echo '<td>' . htmlspecialchars($row['apellidos']) . '</td>';
            echo '<td>' . htmlspecialchars($row['correo']) . '</td>';
            echo '<td>' . htmlspecialchars($row['entradas_unicas']) . '</td>';
            echo '<td>' . htmlspecialchars($row['total_entradas']) . '</td>';
            echo '<td>' . htmlspecialchars($row['total_observaciones']) . '</td>';
            echo '</tr>';
        }

        echo '</table>';
        echo '</html>';
        exit;
    }
}