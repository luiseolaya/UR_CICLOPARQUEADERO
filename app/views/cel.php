<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
            <div class="subtitle">Antes de continuar por favor actualiza tu número telefonico y acepta terminos y condiciones</div>
        </div>
        <form action="/UR_CICLOPARQUEADERO/app/controllers/UsuarioController.php" method="POST">
            <div class="form-floating mb-3">
                <input type="tel" class="form-control" id="floatingInput" name="celular" placeholder="Celular" required pattern="[0-9]{10}">
                <label for="floatingInput">Celular</label>
                <input type="checkbox" name="terminos" value="1" required />
                <a href="https://urosario.edu.co/terminos-y-condiciones">Aceptar términos y condiciones</a>
            </div>
            <button type="submit" name="actualizar_telefono" class="btn btn-primary w-100">Aceptar</button>
        </form>
    </div>
</body>

</html>