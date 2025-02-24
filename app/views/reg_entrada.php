<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar entrada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/UR_CICLOPARQUEADERO/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container text-center">
    <?php
    if (!isset($_SESSION['correo'])) {
        header("Location: /UR_CICLOPARQUEADERO/");
        exit;
    }
    ?>
    <div class="mb-2 border border-secondary text-center mt-5 d-flex align-items-center">
        <img src="/UR_CICLOPARQUEADERO/public/img/LOGOU.png" alt="Logo" class="me-3 ms-4" style="width: 50px; height: auto;">
        <div>
            <div class="fs-2 fw-bolder ms-3">Cicloparqueadero</div>
            <div class="fs-6 fw-bolder mb-2 ms-3">Universidad del Rosario</div>
        </div>
    </div>
    <div><h5>Bienvenido, <?php echo htmlspecialchars($_SESSION['correo']); ?></h5></div>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <form id="entrada-form" action="/UR_CICLOPARQUEADERO/index.php?registrar_entrada=true" method="POST">
        <div id="alerta" class="text-center" style="display: none; padding: 20px; color: white; border-radius: 5px; margin-top: 10px;"></div>
        <div class="fs-2 text-start ms-2 mb-2 mt-2 fw-bolder">+ Entrada</div>
        <div class="text-start ms-3"><p>Favor colocar el código numérico que sale en la parte superior y elegir el color correspondiente</p></div>
        
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
                <option value="1">Parqueadero A</option>
                <option value="2">Parqueadero B</option>
            </select>
        </div>
        <div class="button group btn-group-lg mt-2 d-grid gap-2 ms-2">
            <button type="submit" name="registrar_entrada" id="registrar" class="btn btn-outline-secondary mt-2 mb-4 fs-6">Registrar entrada</button>
        </div>
        <input type="hidden" name="codigo_aleatorio" id="codigo_aleatorio">
        <input type="hidden" name="color_aleatorio" id="color_aleatorio">
        <input type="hidden" name="lat_usuario" id="lat_usuario">
        <input type="hidden" name="lng_usuario" id="lng_usuario">
    </form>
</div>
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
</body>
</html>