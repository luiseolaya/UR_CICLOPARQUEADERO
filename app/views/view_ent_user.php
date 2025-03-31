<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../controllers/UsuarioController.php';
require_once __DIR__ . '/../controllers/EntradaController.php';
use App\Controllers\UsuarioController;
use App\Controllers\EntradaController;

$usuarioController = new UsuarioController();
$entradaController = new EntradaController();

// Obtener el ID del usuario desde la URL
$id_usuario = $_GET['id'] ?? null;

if (!$id_usuario) {
    echo "ID de usuario no proporcionado.";
    exit;
}

// Obtener los datos del usuario
$usuario = $usuarioController->obtenerUsuarioPorId($id_usuario);

// Obtener las entradas del usuario
$entradas = $entradaController->obtenerEntradasPorUsuario($id_usuario);

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
    <title>Ver Entradas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/UR_CICLOPARQUEADERO/public/css/style.css">
    <link  href="/UR_CICLOPARQUEADERO/public/img/icon_U.png" rel="icon" type="image/x-icon" />
</head>
<body>
    <div class="container mt-5">
        <div class="text-center">
            <h2>Entradas Registradas</h2>
            <p>Usuario: <?php echo htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']); ?></p>
        </div>
        <?php if ($entradas): ?>
            <table class="table mt-4">
                <thead>
                    <tr>
                        <th scope="col">ID Entrada</th>
                        <th scope="col">Fecha y Hora</th>
                        <th scope="col">Sede</th>
                        <th scope="col">Evidencia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entradas as $entrada): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($entrada['id_entrada']); ?></td>
                            <td><?php echo htmlspecialchars($entrada['fecha_hora']); ?></td>
                            <td><?php echo htmlspecialchars($entrada['sede_parqueadero']); ?></td>
                            <td>
                                <?php if ($entrada['foto']): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($entrada['foto']); ?>" alt="Foto" style="width: 100px; height: auto;">
                                <?php else: ?>
                                    Sin evidencia
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">No hay entradas registradas para este usuario.</p>
        <?php endif; ?>
        <div class="d-flex justify-content-center mt-4">
            <a href="/UR_CICLOPARQUEADERO/admin_inc" class="btn btn-outline-secondary">Volver</a>
        </div>
    </div>
</body>
</html>
