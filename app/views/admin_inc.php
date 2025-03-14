<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../controllers/UsuarioController.php';

use App\Controllers\UsuarioController;

$usuarioController = new UsuarioController();

// Obtenemos los datos necesarios para mostrar en la vista del administrador
$usuarios = $usuarioController->obtenerTodosLosUsuarios();
$usuariosConMasEntradas = $usuarioController->obtenerUsuariosConMasEntradas();

$search = $_GET['search'] ?? null;
if ($search) {
    $usuarios = $usuarioController->buscarUsuarios($search);
} else {
    $usuarios = $usuarioController->obtenerTodosLosUsuarios();
}
$success = $_GET['success'] ?? null;
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
    <link href="/UR_CICLOPARQUEADERO/public/img/icon_U.png" rel="icon" type="image/x-icon" />
</head>

<body>
    <?php if ($success === '1'): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Usuario actualizado correctamente',
                    showConfirmButton: false,
                    timer: 2100
                });
            });
        </script>
    <?php elseif ($success === '0'): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: 'Error al actualizar el usuario',
                    showConfirmButton: false,
                    timer: 2100
                });
            });
        </script>
    <?php endif; ?>
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
            <div>
                <form action="/UR_CICLOPARQUEADERO/index.php?logout=true" method="POST">
                    <button type="submit" name="logout" class="btn btn-outline-secondary btn-lg">Salir</button>
                </form>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <h3>Usuarios con Más Entradas</h3>
            <div class="text-center">
                <form action="/UR_CICLOPARQUEADERO/index.php" method="GET">
                    <button type="submit" name="generar_reporte" class="btn btn-outline-secondary">
                        <img src="/UR_CICLOPARQUEADERO/public/img/icon_pdf.png" alt="Generar PDF" width="20" height="20" class="me-2"> Generar PDF
                    </button>
                </form>
            </div>
        </div>

        <?php if ($usuariosConMasEntradas): ?>
            <table class="table mt-4">
                <thead>
                    <tr>
                        <th scope="col">Nombres y Apellidos</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Entradas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuariosConMasEntradas as $usuario): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['num_entradas']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-start">No hay registros de entradas todavía.</p>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <h3>Usuarios Registrados</h3>
            <div class="d-flex">
                <form method="GET" action="/UR_CICLOPARQUEADERO/admin_inc" class="d-flex">
                    <input type="text" name="search" class="form-control search-bar" placeholder="Buscar por nombre o correo">
                    <button type="submit" class="btn btn-outline-secondary ms-2">Buscar</button>
                </form>
            </div>
        </div>

        <table class="table mt-4">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nombres y Apellidos</th>
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
                            <a href="edit_user?id=<?php echo htmlspecialchars($usuario['id_usuario']); ?>" class="actions-icon">
                                <img src="/UR_CICLOPARQUEADERO/public/img/edit_icon.png" alt="Modificar" width="20" height="20">
                            </a>
                            <a href="view_ent_user?id=<?php echo htmlspecialchars($usuario['id_usuario']); ?>" class="actions-icon">
                                <img src="/UR_CICLOPARQUEADERO/public/img/view_icon.png" alt="Ver Entradas" width="20" height="20">
                            </a>
                            <a href="javascript:void(0);" onclick="confirmDelete(<?php echo htmlspecialchars($usuario['id_usuario']); ?>)" class="actions-icon">
                                <img src="/UR_CICLOPARQUEADERO/public/img/delete_icon.png" alt="Borrar Usuario" width="20" height="20">
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });

        function confirmDelete(id) {
            swalWithBootstrapButtons.fire({
                title: "¿Estás seguro?",
                text: "¡No podrás revertir esto!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar!",
                cancelButtonText: "No, cancelar",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Enviar solicitud para eliminar el usuario
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/UR_CICLOPARQUEADERO/admin_inc';
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'id_usuario';
                    input.value = id;
                    form.appendChild(input);
                    const deleteInput = document.createElement('input');
                    deleteInput.type = 'hidden';
                    deleteInput.name = 'eliminar_usuario';
                    deleteInput.value = '1';
                    form.appendChild(deleteInput);
                    document.body.appendChild(form);
                    form.submit();
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire({
                        title: "Cancelado",
                        text: "Se ha cancelado la solicitud",
                        icon: "error"
                    });
                }
            });
        }
    </script>
</body>

</html>