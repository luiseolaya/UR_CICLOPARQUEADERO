<?php 
namespace App\Controllers;

use App\Models\Entrada;
use App\Config\Database;
use PDO;
use DateTime;
use DateTimeZone;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/../models/Entrada.php';

class EntradaController {

    private $db;
    private $entrada;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->entrada = new Entrada($this->db);

      
    
    }

    public function obtenerEntradasPorUsuario($id_usuario) {
        $query = "
            SELECT e.id_entrada, e.fecha_hora, p.sede_parqueadero, e.foto
            FROM entrada e
            JOIN parqueadero p ON e.id_parqueadero = p.id_parqueadero
            WHERE e.id_usuario = :id_usuario
            ORDER BY e.fecha_hora DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarEntrada() {
        if (!isset($_SESSION['id_usuario'])) {
            $_SESSION['error'] = 'Debe iniciar sesión para registrar una entrada.';
            header("Location: /UR_CICLOPARQUEADERO/");
            exit;
        }

        if (!empty($_POST)) {
            $codigo_aleatorio = $_POST['codigo_aleatorio'];
            $color_aleatorio = $_POST['color_aleatorio'];
            $latUsuario = $_POST['lat_usuario'];
            $lngUsuario = $_POST['lng_usuario'];
            $idParqueadero = $_POST['id_parqueadero'];

            // Coordenadas de los parqueaderos
            $parqueaderos = [
                1 => ['lat' => 4.601025504132103, 'lng' => -74.07303884639771],  // Parqueadero A
                2 => ['lat' => 66.76717277970545, 'lng' => 175.597189200234],   // Parqueadero B
            ];

            if (!isset($parqueaderos[$idParqueadero])) {
                $_SESSION['error'] = 'Seleccione un parqueadero válido.';
                header("Location: /UR_CICLOPARQUEADERO/reg_entrada");
                exit;
            }

            $latParqueadero = $parqueaderos[$idParqueadero]['lat'];
            $lngParqueadero = $parqueaderos[$idParqueadero]['lng'];
            $rangoMaximo = 100000; // Cambiar según sea necesario

            if ($_POST['codigo'] !== $codigo_aleatorio || $_POST['color'] !== $color_aleatorio || $_POST['id_parqueadero'] == 'Seleccione el Cicloparqueadero') {
                $_SESSION['error'] = 'Ingrese de nuevo el código, color y parqueadero seleccionados.';
                header("Location: /UR_CICLOPARQUEADERO/reg_entrada");
                exit;
            }

            // Validar la distancia entre el usuario y el parqueadero
            $distancia = $this->calcularDistancia($latUsuario, $lngUsuario, $latParqueadero, $lngParqueadero);
            if ($distancia > $rangoMaximo) {
                $_SESSION['error'] = 'Estás fuera del rango permitido.';
                header("Location: /UR_CICLOPARQUEADERO/reg_entrada");
                exit;
            }

            $_SESSION['entrada_temp'] = $_POST;

            // Guardar la entrada en la base de datos
            $this->entrada->id_usuario = $_SESSION['id_usuario'];
            $this->entrada->id_parqueadero = $_POST['id_parqueadero'];
            $this->entrada->fecha_hora = date('Y-m-d H:i:s');
            $this->entrada->fecha_hora = (new DateTime('now', new DateTimeZone('America/Bogota')))->format('Y-m-d H:i:s');

            if ($this->entrada->crearEntrada()) {
                header("Location: /UR_CICLOPARQUEADERO/evidencia");
                exit;
            } else {
                $_SESSION['error'] = 'Error al registrar la entrada. Intente nuevamente.';
                header("Location: /UR_CICLOPARQUEADERO/reg_entrada");
                exit;
            }
        }
    }

    private function calcularDistancia($lat1, $lon1, $lat2, $lon2) {
        $radioTierra = 6371; // Radio de la Tierra en km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $radioTierra * $c;     }
}

if (isset($_POST['registrar_entrada'])) {
    $entradaController = new EntradaController();
    $entradaController->registrarEntrada();
}
?>
<script>
     document.addEventListener('DOMContentLoaded', () => {
        const alerta = document.getElementById('alerta');
        const colores = ['#28a745', '#dc3545', '#ffc107', '#007bff', '#6f42c1']; 

        function seleccionarColorAleatorio() {
            const indiceAleatorio = Math.floor(Math.random() * colores.length);
            return colores[indiceAleatorio];
        }

        function generarMensajeAleatorio() {
            const numeroAleatorio = Math.floor(100000 + Math.random() * 900000);
            return `${numeroAleatorio}`; 
        }

        const colorAleatorio = seleccionarColorAleatorio();
        const mensajeAleatorio = generarMensajeAleatorio();
        
        if (alerta) {
            alerta.style.backgroundColor = colorAleatorio;
            alerta.textContent = mensajeAleatorio;
            alerta.style.display = 'block';
        }

        
        localStorage.setItem('codigo_aleatorio', mensajeAleatorio);
        localStorage.setItem('color_aleatorio', colorAleatorio);

        // Geolocalización y Validación
        function obtenerUbicacion() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(mostrarUbicacion, manejarError);
            } else {
                alert("La geolocalización no es soportada por este navegador.");
            }
        }

        function mostrarUbicacion(position) {
            const latB = position.coords.latitude;
            const lngB = position.coords.longitude;

            document.getElementById('lat_usuario').value = latB;
            document.getElementById('lng_usuario').value = lngB;
        }

        function manejarError(error) {
            alert('No se pudo obtener la ubicación.');
        }

        obtenerUbicacion(); 

        document.getElementById('entrada-form').addEventListener('submit', function(event) {
            // Guardar código y color antes de enviar el formulario
            document.getElementById('codigo_aleatorio').value = localStorage.getItem('codigo_aleatorio');
            document.getElementById('color_aleatorio').value = localStorage.getItem('color_aleatorio');
        });
    });
</script>