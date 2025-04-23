<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../controllers/UsuarioController.php';

use App\Controllers\UsuarioController;

$usuarioController = new UsuarioController();


$usuarios = $usuarioController->obtenerTodosLosUsuarios();
$usuariosConMasEntradas = $usuarioController->obtenerUsuariosConMasEntradas();

$search = $_GET['search'] ?? null;
if ($search) {
    $usuarios = $usuarioController->buscarUsuarios($search);
} else {
    $usuarios = $usuarioController->obtenerTodosLosUsuarios();
}
$success = $_GET['success'] ?? null;
if ($success) {
    echo "<script>
        Swal.fire({
            title: '¡Éxito!',
            text: 'Usuario actualizado correctamente.',
            icon: 'success',
            timer: 4000,
            timerProgressBar: true
        });
    </script>";
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" width="device-width, initial-scale=1.0">
    <title>Administrador Cicloparqueadero UR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/UR_CICLOPARQUEADERO/public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="/UR_CICLOPARQUEADERO/public/img/icon_U.png" rel="icon" type="image/x-icon" />
</head>

<body>
<?php
    if (isset($_SESSION['mensaje'])) {
        echo "<script>
            Swal.fire({
                title: '¡Éxito!',
                text: '" . $_SESSION['mensaje'] . "',
                icon: 'success',
                timer: 4000,
                timerProgressBar: true
            });
            </script>";
        unset($_SESSION['mensaje']);
    }
    ?>
    <div class="container">
        <div class="text-center mb-2 border border-secondary mt-5 d-flex align-items-center">
            <img src="/UR_CICLOPARQUEADERO/public/img/LOGOU.png" alt="Logo" class="me-3 ms-4" style="width: 50px; height: auto;">
            <div>
                <div class="fs-2 fw-bolder ms-3">Administrador Cicloparqueadero</div>
                <div class="fs-6 fw-bolder mb-2 ms-3">Universidad del Rosario</div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5>Bienvenido Administrador, <?php echo htmlspecialchars($_SESSION['correo']); ?></h5>
            </div>
            <div class="d-flex">
                <form action="/UR_CICLOPARQUEADERO/reg_entrada" class="me-2">
                    <a href="/UR_CICLOPARQUEADERO/reg_entrada" class="btn btn-outline-secondary btn-lg">+ Entrada</a>
                </form>
                <form action="/UR_CICLOPARQUEADERO/index.php" method="GET" class="me-2">
                    <button type="submit" name="generar_reporte" class="btn btn-outline-secondary btn-lg">
                        <img src="/UR_CICLOPARQUEADERO/public/img/xls_icon.png" alt="Generar excel" width="20" height="20" class="me-2"> Reporte Excel
                    </button>
                </form>
                <form action="/UR_CICLOPARQUEADERO/index.php?logout=true" method="POST">
                    <button type="submit" name="logout" class="btn btn-outline-secondary btn-lg">Salir</button>
                </form>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <h3>Usuarios con Más Entradas</h3>
        </div>

        <?php if ($usuariosConMasEntradas): ?>
            <table class="table table-striped table-hover mt-4">
                <thead class="table-primary">
                    <tr>
                        <th scope="col">Nombres y Apellidos</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Entradas Únicas por Día</th>
                        <th scope="col">Total de Entradas Registradas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuariosConMasEntradas as $usuario): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['num_entradas']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['total_entradas']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-start">No hay registros de entradas todavía.</p>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <h3>Usuarios Registrados</h3>
            <div class="d-flex flex-column flex-md-row">
                <!-- Formulario de búsqueda -->
                <form method="GET" action="/UR_CICLOPARQUEADERO/ADMINISTRADOR" class="d-flex">
                    <input type="text" name="search" class="form-control search-bar" placeholder="Buscar por nombre o correo">
                    <button type="submit" class="btn btn-outline-secondary ms-2">Buscar</button>
                </form>
            </div>
        </div>

        <table class="mt-4 table justify-content-center">
            <thead>
                <tr>
                    <th scope="col">N documento</th>
                    <th scope="col">Nombres y Apellidos</th>
                    <th scope="col">Correo</th>
                    <th scope="col">Rol</th>
                    <th scope="col">Facultad o area</th>
                    <th scope="col">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($usuario['Ndocumento']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['rol']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['facultad']); ?></td>
                        <td>
                            <a href="edit_user?id=<?php echo htmlspecialchars($usuario['id_usuario']); ?>" class="actions-icon">
                                <img src="/UR_CICLOPARQUEADERO/public/img/edit_icon.png" alt="Modificar" width="20" height="20">
                            </a>
                            <a href="view_ent_user?id=<?php echo htmlspecialchars($usuario['id_usuario']); ?>" class="actions-icon">
                                <img src="/UR_CICLOPARQUEADERO/public/img/view_icon.png" alt="Ver Entradas" width="20" height="20">
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>