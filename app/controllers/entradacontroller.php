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
            $color_aleatorio  = $_POST['color_aleatorio'];
            $latUsuario = isset($_POST['lat_usuario']) ? floatval($_POST['lat_usuario']) : null;
            $lngUsuario = isset($_POST['lng_usuario']) ? floatval($_POST['lng_usuario']) : null;
            $idParqueadero = $_POST['id_parqueadero'];

            // Coordenadas de los parqueaderos
            $parqueaderos = [
                1 => ['lat' => 4.5996990, 'lng' => -74.0734580],  // Claustro
                2 => ['lat' => 4.653844, 'lng' => -74.073169],   // SQM
                3 => ['lat' => 4.774074, 'lng' => -74.035601],  // SEIC
                4 => ['lat' => 4.691297133089843, 'lng' => -74.06198241200046],  // MISI
                5 => ['lat' => 4.6803359, 'lng' => -74.0574497],  // NOVA
            ];

            if (!isset($parqueaderos[$idParqueadero])) {
                $_SESSION['error'] = 'Seleccione un parqueadero válido.';
                header("Location: /UR_CICLOPARQUEADERO/reg_entrada");
                exit;
            }

            $latParqueadero = $parqueaderos[$idParqueadero]['lat'];
            $lngParqueadero = $parqueaderos[$idParqueadero]['lng'];
            $rangoMaximo = 100000; // Cambiar a 0.05 cuando sea necesario KM

            if ($_POST['codigo'] !== $codigo_aleatorio || $_POST['color'] !== $color_aleatorio || $_POST['id_parqueadero'] == 'Seleccione el Cicloparqueadero') {
                $_SESSION['error'] = 'Ingrese de nuevo el código, color y parqueadero seleccionados.';
                header("Location: /UR_CICLOPARQUEADERO/reg_entrada");
                exit;
            }

            // Validar la distancia entre el usuario y el parqueadero si las coordenadas están disponibles
            if ($latUsuario !== null && $lngUsuario !== null) {
                $distancia = $this->calcularDistancia($latUsuario, $lngUsuario, $latParqueadero, $lngParqueadero);
                if ($distancia > $rangoMaximo) {
                    $_SESSION['error'] = 'Estás fuera del rango permitido.';
                    header("Location: /UR_CICLOPARQUEADERO/reg_entrada");
                    exit;
                }
            } else {
                // Si no se pudo obtener la ubicación, mostrar advertencia
                $_SESSION['observaciones'] = 'No se pudo reconocer ubicación';
                $_SESSION['mensaje'] = 'No es posible reconocer su ubicación, por lo que no es posible garantizar su ubicación real, sin embargo se permitirá su registro.';
            }

            $_SESSION['entrada_temp'] = $_POST;

            header("Location: /UR_CICLOPARQUEADERO/evidencia");
            exit;
        }
    }

    public function subirEvidencia() {
        if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['entrada_temp'])) {
            $_SESSION['error'] = 'Primero debes registrar una entrada.';
            header("Location: /UR_CICLOPARQUEADERO/reg_entrada");
            exit;
        }

        
        $query = "SELECT id_usuario FROM usuarios WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_usuario', $_SESSION['id_usuario']);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            $_SESSION['error'] = 'El usuario no existe en la base de datos.';
            header("Location: /UR_CICLOPARQUEADERO/");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['foto'])) {
            $foto = $_POST['foto'];
            $foto = str_replace('data:image/png;base64,', '', $foto);
            $foto = base64_decode($foto);

            // Guardar la entrada en la base de datos
            $this->entrada->id_usuario = $_SESSION['id_usuario'];
            $this->entrada->id_parqueadero = $_SESSION['entrada_temp']['id_parqueadero'];
            $this->entrada->fecha_hora = (new DateTime('now', new DateTimeZone('America/Bogota')))->format('Y-m-d H:i:s');
            $this->entrada->foto = $foto;

            if ($this->entrada->crearEntrada()) {
                unset($_SESSION['entrada_temp']);
                $_SESSION['mensaje'] = "Entrada registrada con éxito.";
                if ($_SESSION['rol'] === 'administrador') {
                    header("Location: /UR_CICLOPARQUEADERO/admin_inc");
                } else {
                    header("Location: /UR_CICLOPARQUEADERO/inicio");
                }
                exit;
            } else {
                $_SESSION['error'] = 'Error al registrar la entrada. Intente nuevamente.';
                header("Location: /UR_CICLOPARQUEADERO/evidencia");
                exit;
            }
        } else {
            $_SESSION['error'] = 'Datos de evidencia no encontrados.';
            header("Location: /UR_CICLOPARQUEADERO/evidencia");
            exit;
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
        return $radioTierra * $c;     
    }
}

if (isset($_POST['registrar_entrada'])) {
    $entradaController = new EntradaController();
    $entradaController->registrarEntrada();
}

if (isset($_POST['subir_evidencia'])) {
    $entradaController = new EntradaController();
    $entradaController->subirEvidencia();
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

        obtenerUbicacion(); 

        document.getElementById('entrada-form').addEventListener('submit', function(event) {
            // Guardar código y color antes de enviar el formulario
            document.getElementById('codigo_aleatorio').value = localStorage.getItem('codigo_aleatorio');
            document.getElementById('color_aleatorio').value = localStorage.getItem('color_aleatorio');
        });
    });
</script>