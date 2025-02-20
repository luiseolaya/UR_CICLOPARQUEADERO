<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = $_SESSION['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/UR_CICLOPARQUEADERO/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    <div class="container">
        <div class="container text-center">
            <div class="mb-4 border border-secondary text-center mt-5 d-flex align-items-center">
                <img src="/UR_CICLOPARQUEADERO/public/img/LOGOU.png" alt="Logo" class="me-3 ms-4" style="width: 50px; height: auto;">
                <div>
                    <div class="fs-2 fw-bolder ms-3">Cicloparqueadero</div>
                    <div class="fs-6 fw-bolder mb-2 ms-3">Universidad del Rosario</div>
                </div>
            </div>
            <div class="btn-group-lg mt-3">
                <button type="button" class="btn btn-outline-secondary mt-3 mb-4 me-4 btn-lg" onclick="mostrarFormulario('iniciar')">Iniciar sesión</button>
                <button type="button" class="btn btn-outline-secondary mt-3 mb-4 me-4 btn-lg" onclick="mostrarFormulario('registro')">Registrarse</button>
            </div>
            <div id="formulario-iniciar" style="display:none;">
                <div class="fs-2 text-start mb-3 mt-4 fw-bolder ms-2">Iniciar sesión</div>
                <form action="/UR_CICLOPARQUEADERO/app/controllers/UsuarioController.php" method="POST">
                    <div class="form-floating mb-4 ms-2">
                        <input type="email" class="form-control" id="floatingInput" name="correo" placeholder="name@example.com" >
                        <label for="floatingInput">Correo Electrónico</label>
                    </div>
                    <div class="form-floating ms-2">
                        <input type="password" class="form-control" id="floatingPassword" name="clave" placeholder="Password" >
                        <label for="floatingPassword">Contraseña</label>
                    </div>
                    <div class="button group btn-group-lg mt-3 d-grid gap-2 ms-2">
                        <button type="submit" name="iniciar" class="btn btn-outline-secondary mt-4 mb-4 fs-6">Entrar</button>
                    </div>
                </form>
            </div>
            <div id="formulario-registro" style="display:none;">
                <div class="fs-2 text-start mb-3 mt-4 fw-bolder ms-2">Registrarse</div>
                <form id="registro-form" action="/UR_CICLOPARQUEADERO/app/controllers/UsuarioController.php" method="POST">
                    <input type="hidden" name="registrar" value="1">
                    <div class="form-floating mb-3 ms-2">
                        <input type="text" class="form-control" id="nombre" name="nombres" placeholder="Nombres" >
                        <label for="nombre">Nombres</label>
                    </div>
                    <div class="form-floating mb-3 ms-2">
                        <input type="text" class="form-control" id="apellido" name="apellidos" placeholder="Apellidos" >
                        <label for="apellido">Apellidos</label>
                    </div>
                    <div class="form-floating mb-3 ms-2">
                        <input type="email" class="form-control" id="correo" name="correo" placeholder="Correo Electrónico" >
                        <label for="correo">Correo Electrónico</label>
                    </div>
                    <div class="form-floating mb-3 ms-2">
                        <input type="text" class="form-control" id="celular" name="celular" placeholder="Celular" >
                        <label for="celular">Celular</label>
                    </div>
                    <div class="form-floating mb-3 ms-2">
                        <input type="password" class="form-control" id="clave" name="clave" placeholder="Clave" >
                        <label for="clave">Clave</label>
                    </div>
                    <div class="form-floating mb-3 ms-2">
                        <input type="password" class="form-control" id="conf_clave" name="conf_clave" placeholder="Confirmar clave" >
                        <label for="conf_clave">Confirmación clave</label>
                    </div>
                    <div class="mt-3 ms-2">
                        <input type="checkbox" id="autorizo" name="autorizo" value="1" >
                        <label for="autorizo"><a href="">Autorizo los términos y condiciones  <a> </label>
                    </div>
                    <div class="button group btn-group-lg mt-3 d-grid gap-2 ms-2">
                        <button type="submit" class="btn btn-outline-secondary mt-4 mb-4 fs-6">Registrarse</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="/UR_CICLOPARQUEADERO/public/js/val_registro.js"></script>
    <script>
        function mostrarFormulario(tipo) {
            document.getElementById('formulario-iniciar').style.display = 'none';
            document.getElementById('formulario-registro').style.display = 'none';

            if (tipo === 'iniciar') {
                document.getElementById('formulario-iniciar').style.display = 'block';
            } else if (tipo === 'registro') {
                document.getElementById('formulario-registro').style.display = 'block';
            }
        }
    </script>
</body>
</html>
