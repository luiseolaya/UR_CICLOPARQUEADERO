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
        $query = "SELECT e.id_entrada, e.fecha_hora, p.sede_parqueadero, e.foto, e.observaciones
                  FROM entrada e
                  JOIN parqueadero p ON e.id_parqueadero = p.id_parqueadero
                  WHERE e.id_usuario = ? 
                  ORDER BY e.fecha_hora DESC";
    
        $stmt = sqlsrv_query($this->db, $query, [$id_usuario]);
    
        $entradas = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $entradas[] = $row;
        }
    
        return $entradas;
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
            $idParqueadero = $_POST['id_parqueadero'];
            $latUsuario = isset($_POST['lat_usuario']) && $_POST['lat_usuario'] !== '' ? floatval($_POST['lat_usuario']) : null;
            $lngUsuario = isset($_POST['lng_usuario']) && $_POST['lng_usuario'] !== '' ? floatval($_POST['lng_usuario']) : null;
            $observaciones = isset($_POST['observaciones']) ? $_POST['observaciones'] : '';

            // Coordenadas de los parqueaderos
            $parqueaderos = [
                1 => ['lat' => 4.5996990, 'lng' => -74.0734580],  // Claustro
                2 => ['lat' => 4.653844, 'lng' => -74.073169],   // SQM
                3 => ['lat' => 4.774074, 'lng' => -74.035601],  // SEIC
                4 => ['lat' => 4.6917010 , 'lng' => -74.0617780],  // MISI
                5 => ['lat' => 4.6803359, 'lng' => -74.0574497],  // NOVA
            ];

            // Validar que el parqueadero seleccionado sea válido
            if (!isset($parqueaderos[$idParqueadero])) {
                $_SESSION['error'] = 'Seleccione un parqueadero válido.';
                header("Location: /UR_CICLOPARQUEADERO/reg_entrada");
                exit;
            }

            $latParqueadero = $parqueaderos[$idParqueadero]['lat'];
            $lngParqueadero = $parqueaderos[$idParqueadero]['lng'];
            $rangoMaximo = 0.50; // Rango máximo en kilómetros

            // Validar código, color y parqueadero
            if ($_POST['codigo'] !== $codigo_aleatorio || $_POST['color'] !== $color_aleatorio || $_POST['id_parqueadero'] == 'Seleccione el Cicloparqueadero') {
                $_SESSION['error'] = 'Ingrese de nuevo el código, color y parqueadero seleccionados.';
                header("Location: /UR_CICLOPARQUEADERO/reg_entrada");
                exit;
            }

            // Caso 1: Si se reciben coordenadas GPS
            if ($latUsuario !== null && $lngUsuario !== null) {
                $distancia = $this->calcularDistancia($latUsuario, $lngUsuario, $latParqueadero, $lngParqueadero);
                if ($distancia > $rangoMaximo) {
                    $_SESSION['error'] = 'Debe estar dentro del rango de 50 kilómetros para registrar la entrada.';
                    header("Location: /UR_CICLOPARQUEADERO/reg_entrada");
                    exit;
                }
            } else {
                // Caso 2: Si no se reciben coordenadas GPS
                $observaciones = 'No se pudo reconocer GPS';
            }

            // Guardar datos temporales de la entrada
            $_SESSION['entrada_temp'] = [
                'id_usuario' => $_SESSION['id_usuario'],
                'id_parqueadero' => $idParqueadero,
                'fecha_hora' => (new DateTime('now', new DateTimeZone('America/Bogota')))->format('Y-m-d H:i:s'),
                'observaciones' => $observaciones
            ];

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

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Método de solicitud inválido.';
            header("Location: /UR_CICLOPARQUEADERO/evidencia");
            exit;
        }

        $fotoRuta = null;

        // Verificar si se recibió una foto en base64
        if (!empty($_POST['foto'])) {
            $fotoBase64 = $_POST['foto'];
            $fotoData = base64_decode(str_replace('data:image/png;base64,', '', $fotoBase64));
            $fotoNombre = uniqid('foto_', true) . '.png';
            $fotoRuta = '/UR_CICLOPARQUEADERO/public/uploads/' . $fotoNombre;
            $destino = $_SERVER['DOCUMENT_ROOT'] . $fotoRuta;

            // Guardar la imagen en el servidor
            if (file_put_contents($destino, $fotoData) === false) {
                $_SESSION['error'] = 'Error al guardar la foto.';
                header("Location: /UR_CICLOPARQUEADERO/evidencia");
                exit;
            }
        }

        $_SESSION['entrada_temp']['foto'] = $fotoRuta;

        // Asignar valores al modelo
        $this->entrada->id_usuario = $_SESSION['entrada_temp']['id_usuario'];
        $this->entrada->id_parqueadero = $_SESSION['entrada_temp']['id_parqueadero'];
        $this->entrada->fecha_hora = (new \DateTime($_SESSION['entrada_temp']['fecha_hora']))->format('Y-m-d H:i:s');
        $this->entrada->foto = $_SESSION['entrada_temp']['foto'];
        $this->entrada->observaciones = $_SESSION['entrada_temp']['observaciones'];

        // Depuración: Verificar los datos antes de guardar
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/UR_CICLOPARQUEADERO/phplogs.txt", "Datos del modelo antes de guardar: " . print_r($this->entrada, true) . "\n", FILE_APPEND);
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/UR_CICLOPARQUEADERO/phplogs.txt", "Datos del modelo: " . print_r($this->entrada, true) . "\n", FILE_APPEND);

        if ($this->entrada->crearEntrada()) {
            unset($_SESSION['entrada_temp']);
            $_SESSION['mensaje'] = "Entrada registrada con éxito.";
            header("Location: " . ($_SESSION['rol'] === 'administrador' ? "/UR_CICLOPARQUEADERO/ADMINISTRADOR" : "/UR_CICLOPARQUEADERO/inicio"));
            exit;
        } else {
            $_SESSION['error'] = 'Error al registrar la entrada.';
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

        document.getElementById('entrada-form').addEventListener('submit', function(event) {
            // Guardar código y color antes de enviar el formulario
            document.getElementById('codigo_aleatorio').value = localStorage.getItem('codigo_aleatorio');
            document.getElementById('color_aleatorio').value = localStorage.getItem('color_aleatorio');
        });
    });
</script>