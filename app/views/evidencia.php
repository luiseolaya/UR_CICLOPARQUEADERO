<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Asegúrate de que el id_parqueadero se almacene en la sesión en la vista anterior
$id_parqueadero = isset($_SESSION['id_parqueadero']) ? $_SESSION['id_parqueadero'] : null;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Evidencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/UR_CICLOPARQUEADERO/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container text-center">
    <?php
    if (!isset($_SESSION['correo'])) {
        header("Location: /UR_CICLOPARQUEADERO/registro");
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
    
    <form id="evidencia-form" action="/UR_CICLOPARQUEADERO/index.php?controller=evidencia&action=subir" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="foto">Tomar Foto:</label>
            <video id="video" width="100%" height="auto" autoplay></video>
            <button id="startbutton" class="btn btn-link">
<img src="/UR_CICLOPARQUEADERO/public/img/camara.png" alt="Cámara" style="width: 35px; height: auto;">
</button>
            <canvas id="canvas" style="display:none;"></canvas>
            <img id="photo" alt="Tu foto" style="display:none;"/>
            <input type="hidden" name="evidencia" id="evidencia">
            <input type="hidden" name="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>"> 
        </div>
        <button type="submit" name="subir_evidencia" class="btn btn-outline-secondary mt-2 mb-4 fs-6">Subir foto</button>
    </form>
</div>
<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const photo = document.getElementById('photo');
    const startbutton = document.getElementById('startbutton');
    const evidencia = document.getElementById('evidencia');

    navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
        .then(stream => {
            video.srcObject = stream;
            video.play();
        })
        .catch(err => {
            console.error("Error al acceder a la cámara: ", err);
        });

    startbutton.addEventListener('click', (event) => {
        event.preventDefault();
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        const data = canvas.toDataURL('image/png');
        photo.setAttribute('src', data);
        photo.style.display = 'block';
        evidencia.value = data;
    });
</script>
</body>
</html>