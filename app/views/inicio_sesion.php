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
    <title>Inicio sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/UR_CICLOPARQUEADERO/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/UR_CICLOPARQUEADERO/public/js/val_inicio.js"></script>
</head>
<body>
    <div class="container text-center">
        <div class="mb-4 border border-secondary text-center mt-5 d-flex align-items-center">
            <img src="/UR_CICLOPARQUEADERO/public/img/LOGOU.png" alt="Logo" class="me-3 ms-4" style="width: 50px; height: auto;">
            <div>
                <div class="fs-2 fw-bolder ms-3">Cicloparqueadero</div>
                <div class="fs-6 fw-bolder mb-2 ms-3">Universidad del Rosario</div>
            </div>
        </div>
        <div class="button group btn-group-lg mt-5">
            <button type="button" id="IniciarSE" class="btn btn-outline-secondary mt-3 mb-4 me-4 btn-lg">Iniciar sesión</button>
            <a href="registro.php">
                <button type="button" id="Registrarse" class="btn btn-outline-secondary mt-3 mb-4 btn-lg">Registrarse</button>
            </a>          
        </div>
        <div class="fs-2 text-start mb-3 mt-4 fw-bolder ms-2">Iniciar sesión</div>

        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        ?>

        <form action="/UR_CICLOPARQUEADERO/app/controllers/UsuarioController.php" method="POST">
            <div class="form-floating mb-4 ms-2">
                <input type="email" class="form-control" id="floatingInput" name="correo" placeholder="name@example.com" required>
                <label for="floatingInput">Correo Electrónico</label>
            </div>
            <div class="form-floating ms-2">
                <input type="password" class="form-control" id="floatingPassword" name="clave" placeholder="Password" required>
                <label for="floatingPassword">Contraseña</label>
            </div>
            <div class="button group btn-group-lg mt-3 d-grid gap-2 ms-2">
                <button type="submit" name="iniciar" class="btn btn-outline-secondary mt-4 mb-4 fs-6">Entrar</button>
            </div>
        </form>
    </div>
    <script src="/UR_CICLOPARQUEADERO/public/js/val_inicio.js"></script>
</body>
</html>
