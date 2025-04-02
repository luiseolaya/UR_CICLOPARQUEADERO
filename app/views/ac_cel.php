<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../controllers/UsuarioController.php';

use App\Controllers\UsuarioController;

$usuarioController = new UsuarioController();
$usuario = $usuarioController->mostrarActualizarTelefono();

$error = $_SESSION['error'] ?? '';
$mensaje = $_SESSION['mensaje'] ?? '';
unset($_SESSION['error'], $_SESSION['mensaje']);
?>
<!DOCTYPE html>
<html lang="es">

<head>   
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Teléfono</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/UR_CICLOPARQUEADERO/public/css/inicio_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="/UR_CICLOPARQUEADERO/public/img/icon_U.png" rel="icon" type="image/x-icon" />
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="/UR_CICLOPARQUEADERO/public/img/LOGOU.png" alt="Logo">
            <div class="title">Cicloparqueadero</div>
            <small>Su número de teléfono actual es <?php echo htmlspecialchars($usuario['celular']); ?></small>
            <div class="subtitle">Ingrese su número telefónico</div>
        </div>
        <form action="/UR_CICLOPARQUEADERO/app/controllers/UsuarioController.php" method="POST">
            <div class="form-floating mb-3">
                <input type="tel" class="form-control" id="floatingInput" name="celular" placeholder="Celular" required pattern="[0-9]{10}" value="<?php echo htmlspecialchars($usuario['celular']); ?>">
                <label for="floatingInput">Celular</label>
            </div>
            <button type="submit" name="actualizar" class="btn btn-primary w-100">Aceptar</button>
        </form>
    </div>
</body>

</html>