<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit();
}
$user = $_SESSION['user'];

require_once '../Controller/UsersReportController.php';

$reportController = new UsersReportController();

// Obtener parámetros de búsqueda y paginación
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Número de usuarios por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Obtener usuarios y contar total de usuarios
$usuarios = $reportController->obtenerUsuarios($offset, $limit, $search);
$totalUsuarios = $reportController->contarUsuarios($search);
$totalPages = $limit > 0 ? ceil($totalUsuarios / $limit) : 1;

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Framework/css/bootstrap.css">
    <link rel="stylesheet" href="../../Framework/custom/userslistview.css">
    <link rel="stylesheet" href="../../Framework/custom/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <title>Lista de usuarios</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Apps</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="../Login/View/ProfileView.php">Mi Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="../index.php">Volver a Gestión de Usuarios</a>
                    </li>
                </ul>
                <span class="navbar-text me-3">
                    Bienvenido, <?php echo htmlspecialchars($user['username']); ?>
                </span>
                <a href="../../Login/Logout/exit.php" class="btn btn-outline-danger">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-12 text-center my-4">
                <h3>Lista de Usuarios</h3>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <form method="GET" action="usersListview.php">
                    <div class="input-group">
                        <select name="limit" class="form-select" onchange="this.form.submit()">
                            <option value="5" <?php if ($limit == 5) echo 'selected'; ?>>5</option>
                            <option value="10" <?php if ($limit == 10) echo 'selected'; ?>>10</option>
                            <option value="25" <?php if ($limit == 25) echo 'selected'; ?>>25</option>
                            <option value="30" <?php if ($limit == 30) echo 'selected'; ?>>30</option>
                            <option value="-1" <?php if ($limit == -1) echo 'selected'; ?>>Todos</option>
                        </select>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </form>
            </div>
            <div class="col-md-6 text-end">
                <input type="text" id="search" class="form-control" placeholder="Buscar usuario" value="<?php echo htmlspecialchars($search); ?>">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12 justify-content-between">
                <a class="btn btn-dark" href="#" id="exportSelected">Exportar Usuarios Seleccionados</a>
                <a class="btn btn-dark" href="exportAllUsers.php">Exportar Todos los Usuarios</a>
            </div>
        </div>
        
        <div id="userTable">
            <div class="row">
                <div class="col-12">
                    <form id="exportForm" method="POST" action="exportSelectedUsers.php">
                        <table class="table table-bordered mt-3">
                            <thead class="table-dark">
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><input type="checkbox" name="selectedUsers[]" value="<?php echo htmlspecialchars($usuario['id']); ?>"></td>
                                    <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['username']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['rol']); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm edit-user" data-id="<?php echo $usuario['id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm delete-user" data-id="<?php echo $usuario['id']; ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>

            <!-- Paginación -->
            <div class="row mb-3">
                <div class="col-12">
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&limit=<?php echo $limit; ?>&search=<?php echo htmlspecialchars($search); ?>" aria-label="Anterior">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&limit=<?php echo $limit; ?>&search=<?php echo htmlspecialchars($search); ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&limit=<?php echo $limit; ?>&search=<?php echo htmlspecialchars($search); ?>" aria-label="Siguiente">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Edición de Usuario -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        <input type="hidden" id="editUserId">
                        <div class="mb-3">
                            <label for="editUsername" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="editUsername" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="editRole" class="form-label">Rol</label>
                            <select class="form-select" id="editRole" required>
                                <!-- Options will be loaded dynamically -->
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../../Framework/js/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
    <script src="../../Framework/js/bootstrap.bundle.min.js"></script>
    <script src="../../Framework/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#search').on('keyup', function() {
                var search = $(this).val();
                var limit = <?php echo $limit; ?>;
                $.ajax({
                    url: 'usersListview.php',
                    type: 'GET',
                    data: {
                        search: search,
                        limit: limit,
                        ajax: 1
                    },
                    success: function(data) {
                        $('#userTable').html($(data).find('#userTable').html());
                    }
                });
            });

            // Select all checkboxes
            $('#selectAll').click(function() {
                $('input[name="selectedUsers[]"]').prop('checked', this.checked);
            });

            // Export selected users
            $('#exportSelected').click(function() {
                $('#exportForm').submit();
            });

            // Delete user
            $('.delete-user').click(function() {
                var userId = $(this).data('id');
                var row = $(this).closest('tr');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrás revertir esto!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, borrar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'deleteUser.php',
                            type: 'POST',
                            data: { id: userId },
                            success: function(response) {
                                var jsonResponse = JSON.parse(response);
                                if (jsonResponse.success) {
                                    Swal.fire(
                                        '¡Borrado!',
                                        'El usuario ha sido borrado.',
                                        'success'
                                    );
                                    row.remove();
                                } else {
                                    Swal.fire(
                                        'Error',
                                        'Hubo un problema al borrar el usuario.',
                                        'error'
                                    );
                                }
                            }
                        });
                    }
                });
            });

            // Edit user
            $('.edit-user').click(function() {
                var userId = $(this).data('id');
                $.ajax({
                    url: 'editUser.php',
                    type: 'GET',
                    data: { id: userId },
                    success: function(response) {
                        var data = JSON.parse(response);
                        var usuario = data.usuario;
                        var roles = data.roles;

                        $('#editUserId').val(usuario.id);
                        $('#editUsername').val(usuario.username);
                        $('#editEmail').val(usuario.email);

                        $('#editRole').empty();
                        roles.forEach(function(role) {
                            var selected = role.id == usuario.role_id ? 'selected' : '';
                            $('#editRole').append('<option value="' + role.id + '" ' + selected + '>' + role.nombre + '</option>');
                        });

                        $('#editUserModal').modal('show');
                    }
                });
            });

            // Update user
            $('#editUserForm').submit(function(event) {
                event.preventDefault();

                var userId = $('#editUserId').val();
                var username = $('#editUsername').val();
                var email = $('#editEmail').val();
                var role_id = $('#editRole').val();

                $.ajax({
                    url: 'editUser.php',
                    type: 'POST',
                    data: {
                        id: userId,
                        username: username,
                        email: email,
                        role_id: role_id
                    },
                    success: function(response) {
                        var jsonResponse = JSON.parse(response);
                        if (jsonResponse.success) {
                            Swal.fire(
                                '¡Actualizado!',
                                'El usuario ha sido actualizado.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Error',
                                'Hubo un problema al actualizar el usuario.',
                                'error'
                            );
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>
