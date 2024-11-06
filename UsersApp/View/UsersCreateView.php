<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit();
}
$user = $_SESSION['user'];

require_once '../../Config/Database.php';
require_once '../../Login/Controller/RegisterController.php';

$registerController = new Usuario();
$roles = $registerController->getRoles();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Framework/css/bootstrap.css">
    <link rel="stylesheet" href="../../Framework/custom/crearusuario.css">
    <link rel="stylesheet" href="../../Framework/custom/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <title>Crear Usuarios</title>
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
                <a href="../Login/Logout/exit.php" class="btn btn-outline-danger">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container main-container">
        <div class="row">
            <div class="col-12 text-center">
                <h3 class="crea-usuario">Crear Usuarios</h3>
            </div>
        </div>
        <div class="form-container">
            <form id="createUserForm">
                <div class="mb-3">
                    <label for="username" class="form-label">Nombre de Usuario</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="role_id" class="form-label">Rol</label>
                    <select class="form-select" id="role_id" name="role_id" required>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../Framework/js/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
    <script src="../../Framework/js/bootstrap.bundle.min.js"></script>
    <script src="../../Framework/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#createUserForm').on('submit', function(event) {
                event.preventDefault();
                $.ajax({
                    url: 'procesar_crear_usuario.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        var jsonData = JSON.parse(response);

                        if (jsonData.success == "1") {
                            Swal.fire({
                                icon: 'success',
                                title: 'Usuario creado',
                                text: 'El usuario ha sido creado exitosamente',
                            });
                            $('#createUserForm')[0].reset();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo crear el usuario',
                            });
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
