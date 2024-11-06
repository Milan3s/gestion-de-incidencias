<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit();
}

$user = $_SESSION['user'];
$userId = $user['id']; // Obtener el ID del usuario desde la sesión

require_once '../../Login/Controller/ProfileDetailsController.php';
require_once '../../Login/Controller/EditProfileDetailsController.php';

$controller = new ProfileDetailsController();
$userDetails = $controller->getUserProfileDetails($userId);

$editController = new EditProfileDetailsController();
$roles = $editController->getRoles();
$secciones = $editController->getSecciones();

$status = isset($_GET['status']) ? $_GET['status'] : '';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Framework/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../Framework/custom/profileView.css">
    <link rel="stylesheet" href="../../Framework/custom/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <title>Detalles de mi cuenta</title>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Mi perfil</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="../../welcome/index.php">Panel</a>
                        </li>
                    </ul>
                    <span class="navbar-text me-3">
                        Bienvenido, <?php echo htmlspecialchars($user['username']); ?>
                    </span>
                    <a href="../Logout/exit.php" class="btn btn-outline-danger">Cerrar Sesión</a>
                </div>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="profile-details">
            <h2>Detalles del Usuario</h2>
            <?php
            if ($userDetails) {
                echo "<p><img src='../../Login/fotos_users/{$userDetails['photo_user']}' alt='Foto de Perfil' id='foto_perfil' /></p>";
                echo "<div class='texto-del-detalle'>";
                echo "<p><label>Nombre de Usuario:</label> <span id='username'>{$userDetails['username']}</span></p>";
                echo "<p><label>Correo Electrónico:</label> <span id='email'>{$userDetails['email']}</span></p>";
                echo "<p><label>Nombre:</label> <span id='first_name'>{$userDetails['first_name']}</span></p>";
                echo "<p><label>Apellidos:</label> <span id='last_name'>{$userDetails['last_name']}</span></p>";
                echo "<p><label>Dirección:</label> <span id='address'>{$userDetails['address']}</span></p>";
                echo "<p><label>Teléfono:</label> <span id='phone_number'>{$userDetails['phone_number']}</span></p>";
                echo "<p><label>Fecha de Nacimiento:</label> <span id='birth_date'>{$userDetails['birth_date']}</span></p>";
                echo "<p><label>Rol en la empresa :</label> <span id='role'>{$userDetails['role']}</span></p>";
                echo "<p><label>Sección:</label> <span id='seccion'>{$userDetails['seccion']}</span></p>";
                echo "</div>";
                // Agregar botón "E" aquí
                echo "<div class='edit-button-container'>";
                echo "<button type='button' class='btn btn-outline-dark edit-button' data-bs-toggle='modal' data-bs-target='#editProfileModal'>E</button>";
                echo "</div>";
            } else {
                echo "<p>No se encontraron detalles del usuario.</p>";
            }
            ?>
        </div>
    </div>

    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Editar Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editProfileForm" action="updateProfile.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                        <input type="hidden" name="current_photo_user" value="<?php echo $userDetails['photo_user']; ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editUsername" class="form-label">Nombre de Usuario</label>
                                    <input type="text" class="form-control" id="editUsername" name="username" value="<?php echo $userDetails['username']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="editEmail" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="editEmail" name="email" value="<?php echo $userDetails['email']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="editFirstName" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="editFirstName" name="first_name" value="<?php echo $userDetails['first_name']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="editLastName" class="form-label">Apellidos</label>
                                    <input type="text" class="form-control" id="editLastName" name="last_name" value="<?php echo $userDetails['last_name']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="editAddress" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="editAddress" name="address" value="<?php echo $userDetails['address']; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editPhone" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="editPhone" name="phone_number" value="<?php echo $userDetails['phone_number']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="editBirthDate" class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control" id="editBirthDate" name="birth_date" value="<?php echo $userDetails['birth_date']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="editRole" class="form-label">Rol en la empresa</label>
                                    <select class="form-control" id="editRole" name="role_id">
                                        <?php
                                        foreach ($roles as $role) {
                                            $selected = $role['id'] == $userDetails['role_id'] ? 'selected' : '';
                                            echo "<option value='{$role['id']}' {$selected}>{$role['nombre']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="editSeccion" class="form-label">Sección</label>
                                    <select class="form-control" id="editSeccion" name="seccion_id">
                                        <?php
                                        foreach ($secciones as $seccion) {
                                            $selected = $seccion['id'] == $userDetails['seccion_id'] ? 'selected' : '';
                                            echo "<option value='{$seccion['id']}' {$selected}>{$seccion['nombre']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="editPhoto" class="form-label">Foto de Perfil</label>
                                    <input type="file" class="form-control" id="editPhoto" name="photo_user">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../../Framework/js/jquery-3.7.1.js"></script>
    <script src="../../Framework/js/bootstrap.bundle.min.js"></script>
    <script src="../../Framework/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const status = "<?php echo $status; ?>";
            if (status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Perfil actualizado',
                    text: 'Tu perfil ha sido actualizado exitosamente.'
                });
            } else if (status === 'error') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al actualizar tu perfil. Inténtalo de nuevo.'
                });
            }
        });
    </script>
</body>

</html>
