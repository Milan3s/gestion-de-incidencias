<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login y Registro</title>
    <link href="" rel="stylesheet">
    <link rel="stylesheet" href="../../Framework/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../Framework/custom/LoginView.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center">Acceso</h2>
                        <?php
                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
                            require_once '../Controller/LoginController.php';
                            $loginController = new LoginController();

                            $username = $_POST['username'];
                            $password = $_POST['password'];

                            if ($loginController->login($username, $password)) {
                                header('Location: ../../welcome/index.php');
                                exit();
                            } else {
                                echo "<div class='alert alert-danger'>Nombre de usuario o contraseña incorrectos.</div>";
                            }
                        }
                        ?>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="loginUsername" class="form-label">Nombre de usuario</label>
                                <input type="text" class="form-control" id="loginUsername" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="loginPassword" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="loginPassword" name="password" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary w-100">Iniciar sesión</button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="RegisterView.php">¿No tienes una cuenta? Regístrate</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <link rel="stylesheet" href="../../Framework/js/jquery-3.7.1.js">
    <link rel="stylesheet" href="../../Framework/js/bootstrap.min.js">

</body>
</html>
