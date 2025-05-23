<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../controllers/UsuarioController.php';
require_once __DIR__ . '/../controllers/sessionController.php';
use App\Controllers\UsuarioController;

$usuarioController = new UsuarioController();


$data = $usuarioController->mostrarUsuarioYEntradas();
$usuario = $data['usuario'];
$entradas = $data['entradas'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/cicloparqueaderos/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link  href="/cicloparqueaderos/public/img/icon_U.png" rel="icon" type="image/x-icon" />
</head>
<body>
    <?php
    if (isset($_SESSION['mensaje'])) {
        echo "<script>
            Swal.fire({
                title: '¡Éxito!',
                text: '" . $_SESSION['mensaje'] . "',
                icon: 'success',
                timer: 4000,
                timerProgressBar: true
            });
            </script>";
        unset($_SESSION['mensaje']);
    }
    ?>
    <div class="container text-center">
        <div class="mb-2 border border-secondary text-center mt-5 d-flex align-items-center">
            <img src="/cicloparqueaderos/public/img/LOGOU.png" alt="Logo" class="me-3 ms-4" style="width: 50px; height: auto;">
            <div>
                <div class="fs-2 fw-bolder ms-3">Cicloparqueadero</div>
                <div class="fs-6 fw-bolder mb-2 ms-3">Universidad del Rosario</div>
            </div>
        </div>
        <div><h5>Bienvenido <?php echo htmlspecialchars($usuario['correo']);?>
		
		</h5>
		<img src="/cicloparqueaderos/public/img/selloefr.png" alt="sello" class="me-3 ms-4" style="width: 100px; height: auto;">
    </div>
        <div class="d-flex justify-content-between mt-3 mb-4">
            <form action="/cicloparqueaderos/reg_entrada">
            <a href="/cicloparqueaderos/reg_entrada" class="btn btn-outline-secondary btn-lg">+ Entrada</a>
            </form>
            <form action="/cicloparqueaderos/index.php?logout=true" method="POST">
            <button type="submit" name="logout" class="btn btn-outline-secondary btn-lg">Salir</button>
            <form action="/cicloparqueaderos/actualizar_telefono">
            <a href="/cicloparqueaderos/actualizar_telefono" class="btn btn-outline-secondary btn-lg">Actualizar telefono</a>
            </form>
        </div>
        <div>
            <table class="table mt-4" id="tablaParqueadero">
                <thead>
                    <tr>
                        <th scope="col">&#x2611;&#xfe0f;</th>
                        <th scope="col">Fecha</th>
                        <th scope="col">Sede</th>
                        <th scope="col">Evento</th>
                        <th scope="col">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entradas as $entrada): ?>
                        <tr>
                            <th scope="row">&#x2611;&#xfe0f;</th>
                            <td><?php 
							echo htmlspecialchars($entrada['fecha_hora']); ?></td>
                            <td><?php echo htmlspecialchars($entrada['sede_parqueadero']); ?></td>
                            <td>Entrada</td>
                            <td>
                                <form action="/cicloparqueaderos/index.php?controller=entrada&action=salida" method="POST">
                                    <input type="hidden" name="id_entrada" value="<?php echo htmlspecialchars($entrada['id_entrada']); ?>">
                                    
                                        <img src="/cicloparqueaderos/public/img/Salida.png" width="30px" height="30px">
                                    
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
	
	<script>
    window.onpageshow = function(event) {
        if (event.persisted) {
            // Si la página viene del cache (navegador), forzar recarga
            window.location.reload();
        }
    };
</script>
</body>
</html>