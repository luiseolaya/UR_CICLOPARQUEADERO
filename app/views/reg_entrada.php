<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../controllers/sessionController.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar entrada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/cicloparqueaderos/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link  href="/cicloparqueaderos/public/img/icon_U.png" rel="icon" type="image/x-icon" />
</head>
<body>
<div class="container text-center">
    <?php
    if (!isset($_SESSION['correo'])) {
        header("Location: /cicloparqueaderos/");
        exit;
    }
    ?>
    <div class="mb-2 border border-secondary text-center mt-5 d-flex align-items-center">
        <img src="/cicloparqueaderos/public/img/LOGOU.png" alt="Logo" class="me-3 ms-4" style="width: 50px; height: auto;">
        <div>
            <div class="fs-2 fw-bolder ms-3">Cicloparqueadero</div>
            <div class="fs-6 fw-bolder mb-2 ms-3">Universidad del Rosario</div>
        </div>
    </div>
    <div><h5><?php echo htmlspecialchars($_SESSION['nombres']); ?></h5>
	 <img src="/cicloparqueaderos/public/img/selloefr.png" alt="sello" class="me-3 ms-4" style="width: 100px; height: auto;">
	</div>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['mensaje'])): ?>
        <script>
            Swal.fire({
                title: '¡Atención!',
                text: '<?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>',
                icon: 'warning',
                timer: 4000,
                timerProgressBar: true
            });
        </script>
    <?php endif; ?>
    <?php if (isset($_SESSION['alerta'])): ?>
        <script>
            Swal.fire({
                title: '¡Atención!',
                text: '<?php echo $_SESSION['alerta']; unset($_SESSION['alerta']); ?>',
                icon: 'info',
                timer: 5000,
                timerProgressBar: true
            });
        </script>
    <?php endif; ?>
    <form id="entrada-form" action="/cicloparqueaderos/index.php" method="POST">
        <input type="hidden" name="registrar_entrada" value="1">
        <?php
        $colores = ['#28a745', '#dc3545', '#ffc107', '#007bff', '#6f42c1'];

        $colorAleatorio = $colores[array_rand($colores)];

        $codigoAleatorio = rand(100000, 999999);
        ?>
        <div id="alerta" class="text-center" style="display: none; padding: 20px; color: white; border-radius: 5px; margin-top: 10px; background-color:<?php
        echo $colorAleatorio;?>;"><?php echo $codigoAleatorio;?>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <div class="fs-2 text-start ms-2 mb-2 mt-2 fw-bolder">+ Entrada</div>
            <a href="#" onclick="redirectUser()">
                <button type="button" class="btn btn-outline-secondary mt-3 mb-4 ms-4 btn-sm fs-6">Regresar</button>
            </a>
            <script>
                function redirectUser() {
                    const userRole = "<?php echo $_SESSION['rol']; ?>";
                    if (userRole === 'administrador') {
                        window.location.href = "/cicloparqueaderos/ADMINISTRADOR";
                    } else {
                        window.location.href = "/cicloparqueaderos/inicio";
                    }
                }
            </script>
        </div>
        <div class="text-start ms-3"><p>Por favor colocar el código numérico que sale en la parte superior y elegir el color correspondiente</p></div>
        
        <div class="form-floating mb-3 ms-2">
            <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Ingrese el código" required>
            <label for="codigo">Ingrese el código</label> 
        </div>
        <div class="form-floating mb-3 ms-2">
            <select class="form-select" id="EscogerColor" name="color" required>
                <option selected>Seleccione el color</option>
                <option value="#28a745">Verde</option>
                <option value="#ffc107">Amarillo</option>
                <option value="#007bff">Azul</option>
                <option value="#dc3545">Rojo</option>
                <option value="#6f42c1">Morado</option>
            </select>
        </div>
        <div class="form-floating mb-3 ms-2">
            <select class="form-select" id="Parqueadero" name="id_parqueadero" required>
                <option selected>Seleccione el Cicloparqueadero</option>
                <option value="1">Claustro</option>
                <option value="2">SQM</option>
                <option value="3">SEIC</option>
                <option value="4">MISI</option>
                <option value="5">NOVA</option>
            </select>
        </div>
        <div class="button group btn-group-lg mt-2 d-grid gap-2 ms-2">
            <button type="submit" name="registrar_entrada" id="registrar" class="btn btn-outline-secondary mt-2 mb-4 fs-6">Registrar entrada</button>
        </div>
        <input type="hidden" name="codigo_aleatorio" id="codigo_aleatorio">
        <input type="hidden" name="color_aleatorio" id="color_aleatorio">
        <input type="hidden" name="lat_usuario" id="lat_usuario">
        <input type="hidden" name="lng_usuario" id="lng_usuario">
        <input type="hidden" name="observaciones" id="observaciones">
    </form>
</div>
<script>
    function obtenerUbicacion() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(mostrarUbicacion, manejarError);
        } else {
            manejarError({ code: 0 });
        }
    }

    function mostrarUbicacion(position) {
        const latB = position.coords.latitude;
        const lngB = position.coords.longitude;

        // Asignar las coordenadas a los campos ocultos
        document.getElementById('lat_usuario').value = latB;
        document.getElementById('lng_usuario').value = lngB;
    }

    function manejarError(error) {
        let mensaje = '';
        switch (error.code) {
            case error.PERMISSION_DENIED:
                mensaje = 'El usuario denegó el permiso de geolocalización.';
                break;
            case error.POSITION_UNAVAILABLE:
                mensaje = 'La información de ubicación no está disponible.';
                break;
            case error.TIMEOUT:
                mensaje = 'El tiempo de espera para obtener la ubicación se agotó.';
                break;
            default:
                mensaje = 'Ocurrió un error desconocido.';
                break;
        }

        // Limpiar los campos de coordenadas si ocurre un error
        document.getElementById('lat_usuario').value = '';
        document.getElementById('lng_usuario').value = '';

        // Mostrar alerta al usuario
        Swal.fire({
            position: "top",
            icon: "warning",
            title: "<h5 style='font-size: 16px; font-weight: bold;'>No es posible reconocer su ubicación</h5>",
            html: "<p style='font-size: 14px;'>No es posible garantizar su ubicación real, sin embargo se permitirá su registro.</p><p style='font-size: 12px; color: gray;'>" + mensaje + "</p>",
            confirmButtonText: "Aceptar",
            width: "300px", // Reduce el ancho de la alerta
            padding: "30px", // Reduce el padding interno
        });
    }

    // Asegurarse de que el valor de observaciones se envíe correctamente
    document.getElementById('entrada-form').addEventListener('submit', function(event) {
        const latUsuario = parseFloat(document.getElementById('lat_usuario').value);
        const lngUsuario = parseFloat(document.getElementById('lng_usuario').value);
        const idParqueadero = document.getElementById('Parqueadero').value;

        // Coordenadas de los parqueaderos
        const parqueaderos = {
            1: {lat: 4.5996990, lng: -74.0734580},
            2: {lat: 4.653844, lng: -74.073169},
            3: {lat: 4.774074, lng: -74.035601},
            4: {lat: 4.6917010, lng: -74.0617780},
            5: {lat: 4.6803359, lng: -74.0574497}
        };

        // Si no hay coordenadas, establecer observación
        if (!latUsuario || !lngUsuario) {
            document.getElementById('observaciones').value = 'GPS fuera del rango,posible fallo en hardware,ubicación no garantizada';
            return; // Permite el envío normal, el backend lo maneja igual
        }

        // Si hay coordenadas y parqueadero seleccionado, validar distancia
        if (parqueaderos[idParqueadero]) {
            const latP = parqueaderos[idParqueadero].lat;
            const lngP = parqueaderos[idParqueadero].lng;

            // Calcular distancia en km
            function calcularDistancia(lat1, lon1, lat2, lon2) {
                const R = 6371;
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLon = (lon2 - lon1) * Math.PI / 180;
                const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                    Math.sin(dLon/2) * Math.sin(dLon/2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                return R * c;
            }

            const distancia = calcularDistancia(latUsuario, lngUsuario, latP, lngP);

            if (distancia > 0.05) { // 50 metros
                event.preventDefault();
                Swal.fire({
                    title: '¡Atención!',
                    text: 'Debe estar dentro del rango de 50 metros para registrar la entrada, pero de igual manera puede registrar su entrada.',
                    icon: 'info',
                    confirmButtonText: 'Registrar de todas formas'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('observaciones').value = 'GPS fuera del rango,posible fallo en hardware,ubicación no garantizada';
                        // Quita el event listener antes de enviar para evitar doble submit
                        document.getElementById('entrada-form').onsubmit = null;
                        this.submit();
                    }
                });
            }
        }
    });

    // Llamar a la función para obtener la ubicación al cargar la página
    obtenerUbicacion();
</script>
</body>
</html>