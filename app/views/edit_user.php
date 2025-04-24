<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../controllers/UsuarioController.php';

use App\Controllers\UsuarioController;

$usuarioController = new UsuarioController();

$id_usuario = $_GET['id'] ?? null;

if (!$id_usuario) {
    echo "ID de usuario no proporcionado.";
    exit;
}

$usuario = $usuarioController->obtenerUsuarioPorId($id_usuario);

if (!$usuario) {
    echo "Usuario no encontrado.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/UR_CICLOPARQUEADERO/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="/UR_CICLOPARQUEADERO/public/img/icon_U.png" rel="icon" type="image/x-icon" />
</head>

<body>

    <div class="container text-center">
        <div class="mb-2 border border-secondary text-center mt-5 d-flex align-items-center">
            <img src="/UR_CICLOPARQUEADERO/public/img/LOGOU.png" alt="Logo" class="me-3 ms-4" style="width: 50px; height: auto;">
            <div>
                <div class="fs-2 fw-bolder ms-3">Administrador Cicloparqueadero</div>
                <div class="fs-6 fw-bolder mb-2 ms-3">Universidad del Rosario</div>
            </div>
        </div>
        <div class="container mt-5">
            <div class="text-center">
                <h2>Actualizar rol o numeró de contacto</h2>
            </div>
            <br>
            <br>
            <div class="mb-3">
                <h3>Nombres y Apellidos</h3>
                <?php echo htmlspecialchars($usuario['nombres']); ?>  <?php echo htmlspecialchars($usuario['apellidos']); ?>
            </div>
            <div class="mb-3">
                <h3>Correo Electrónico</h3>
                <?php echo htmlspecialchars($usuario['correo']); ?>" 
            </div>
            <form action="/UR_CICLOPARQUEADERO/edit_user" method="POST" class="mt-4">
                <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($usuario['id_usuario']); ?>">
                <div class="mb-3">
                    <label for="celular" class="form-label">Celular</label>
                    <input type="tel" class="form-control" id="celular" name="celular" value="<?php echo htmlspecialchars($usuario['celular']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="rol" class="form-label">Rol</label>
                    <select class="form-select" id="rol" name="rol" required>
                        <option value="usuario" <?php echo $usuario['rol'] == 'usuario' ? 'selected' : ''; ?>>Usuario</option>
                        <option value="administrador" <?php echo $usuario['rol'] == 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="/UR_CICLOPARQUEADERO/ADMINISTRADOR" class="btn btn-primary">Cancelar</a>
                    <button type="submit" name="guardar_usuario" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
</body>

</html>