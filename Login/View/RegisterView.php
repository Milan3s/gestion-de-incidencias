<?php
require_once '../Controller/LoginController.php';
require_once '../Controller/RegisterController.php';

$registrationSuccess = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    $usuario = new Usuario();
    if ($usuario->createUsuario($username, $password, $email)) {
        $registrationSuccess = true;
    } else {
        $registrationSuccess = false;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <link href="../../Framework/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../Framework/custom/RegisterView.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="form-container">
                    <div class="form-body">
                        <h2 class="form-title text-center">Registrarse</h2>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Nombre de usuario</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Registrarse</button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="LoginView.php">¿Ya tienes una cuenta? Inicia sesión</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../../Framework/js/jquery-3.7.1.js"></script>
    <script src="../../Framework/js/bootstrap.bundle.min.js"></script>
    <script src="../../Framework/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        <?php if ($registrationSuccess === true): ?>
        Swal.fire({
            icon: 'success',
            title: 'Registro exitoso',
            text: 'Tu cuenta ha sido creada exitosamente',
            confirmButtonText: 'Iniciar Sesión',
            showCancelButton: true,
            cancelButtonText: 'Cerrar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'LoginView.php';
            }
        });
        <?php elseif ($registrationSuccess === false): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error al registrar',
            text: 'Hubo un problema al crear tu cuenta. Por favor, intenta de nuevo',
            confirmButtonText: 'Intentar de nuevo'
        });
        <?php endif; ?>
    </script>
</body>
</html>
