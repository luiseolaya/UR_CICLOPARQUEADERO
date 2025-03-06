<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../controllers/UsuarioController.php';
use App\Controllers\UsuarioController;

$usuarioController = new UsuarioController();

// Obtenemos los datos necesarios para mostrar en la vista del administrador
$usuarios = $usuarioController->obtenerTodosLosUsuarios();
$usuarioConMasEntradas = $usuarioController->obtenerUsuarioConMasEntradas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador Cicloparqueadero UR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/UR_CICLOPARQUEADERO/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container text-center">
        <div class="mb-2 border border-secondary text-center mt-5 d-flex align-items-center">
            <img src="/UR_CICLOPARQUEADERO/public/img/LOGOU.png" alt="Logo" class="me-3 ms-4" style="width: 50px; height: auto;">
            <div>
                <div class="fs-2 fw-bolder ms-3">Administrador Cicloparqueadero </div>
                <div class="fs-6 fw-bolder mb-2 ms-3">Universidad del Rosario</div>
            </div>
        </div>
        <div><h5>Bienvenido Administrador, <?php echo htmlspecialchars($_SESSION['correo']); ?></h5></div>
        
        <div class="d-flex justify-content-end mt-3 mb-4">
            <form action="/UR_CICLOPARQUEADERO/index.php?logout=true" method="POST">
            <button type="submit" name="logout" class="btn btn-outline-secondary btn-lg">Salir</button>
            </form>
        </div>
        <h3 class="mt-4">Usuarios Registrados</h3>
        <table class="table mt-4">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Correo</th>
                    <th scope="col">Rol</th>
                    <th scope="col">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($usuario['id_usuario']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['rol']); ?></td>
                        <td>
                            <form action="../app/controllers/UsuarioController.php" method="POST">
                                <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($usuario['id_usuario']); ?>">
                                <select name="nuevo_rol" class="form-select">
                                    <option value="usuario" <?php echo $usuario['rol'] == 'usuario' ? 'selected' : ''; ?>>Usuario</option>
                                    <option value="administrador" <?php echo $usuario['rol'] == 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                                </select>
                                <button type="submit" name="cambiar_rol" class="btn btn-outline-secondary mt-2">Cambiar Rol</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3 class="mt-4">Usuario con Más Entradas</h3>
        <?php if ($usuarioConMasEntradas): ?>
            <p><?php echo htmlspecialchars($usuarioConMasEntradas['nombres'] . ' ' . $usuarioConMasEntradas['apellidos']); ?> con <?php echo htmlspecialchars($usuarioConMasEntradas['num_entradas']); ?> entradas.</p>
        <?php else: ?>
            <p>No hay registros de entradas todavía.</p>
        <?php endif; ?>

        <form action="../app/controllers/ReporteController.php" method="POST">
            <button type="submit" name="generar_reporte" class="btn btn-outline-secondary mt-4">Generar Reporte PDF</button>
        </form>
    </div>
</body>
</html>
