<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../controllers/sessionController.php';

$error = $_SESSION['error'] ?? '';
$inactividad = $_SESSION['inactividad'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/cicloparqueaderos/public/css/inicio_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="/cicloparqueaderos/public/img/icon_U.png" rel="icon" type="image/x-icon" />
</head>

<body>
    <?php if ($error): ?>
        <script>
            Swal.fire({
                title: 'Error',
                text: '<?php echo htmlspecialchars($error); ?>',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        </script>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
	<?php if ($inactividad): ?>
        <script>
            Swal.fire({
                title: 'Sesión caducada',
                text: '<?php echo htmlspecialchars($inactividad); ?>',
                icon: 'warning',
                confirmButtonText: 'Aceptar'
            });
        </script>
        <?php unset($_SESSION['inactividad']); ?>
    <?php endif; ?>
    <div class="login-container">
        <div class="login-header">
            <img src="/cicloparqueaderos/public/img/LOGOU.png" alt="Logo">
            <div class="title">Cicloparqueadero</div>
            <div class="subtitle">Universidad del Rosario</div>
        </div>
        <form action="/cicloparqueaderos/app/controllers/UsuarioController.php" method="POST">
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="floatingInput" name="correo" placeholder="name@example.com">
                <label for="floatingInput">Correo Electrónico</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="floatingPassword" name="clave" placeholder="Password">
                <label for="floatingPassword">Contraseña</label>
            </div>
            <button type="submit" name="iniciar" class="btn btn-primary w-100">Entrar</button>
        </form>
    </div>
</body>

</html>