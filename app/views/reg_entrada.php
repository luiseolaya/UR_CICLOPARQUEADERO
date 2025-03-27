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
    <link  href="/UR_CICLOPARQUEADERO/public/img/icon_U.png" rel="icon" type="image/x-icon" />
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
    <div><h5><?php echo htmlspecialchars($_SESSION['correo']); ?></h5></div>
    
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
    <form id="entrada-form" action="/UR_CICLOPARQUEADERO/index.php?registrar_entrada=true" method="POST">
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
            <a href="/UR_CICLOPARQUEADERO/inc_user">
                <button type="button" class="btn btn-outline-secondary mt-3 mb-4 ms-4 btn-sm fs-6">Regresar</button>
            </a>
        </div>
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
    </form>
</div>
</body>
</html>