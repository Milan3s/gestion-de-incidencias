<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit();
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Framework/css/bootstrap.min.css">
    <link rel="stylesheet" href="../Framework/custom/welcome.css">
    <link rel="stylesheet" href="../Framework/custom/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Pantallazo</title>
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
                </ul>
                <span class="navbar-text me-3">
                    Bienvenido, <?php echo htmlspecialchars($user['username']); ?>
                </span>
                <a href="../Login/Logout/exit.php" class="btn btn-outline-danger">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container center-content">
        <div class="welcome-title">Bienvenido al Panel</div>
        <div class="link-container">
            <div class="text-center">
                <a href="../IncidentApp/index.php" class="btn btn-primary btn-custom w-100">
                    <i class="fas fa-tasks"></i> 
                    APP de Incidencias
                </a>
            </div>
            <div class="text-center">
                <a href="../NoteApp/index.php" class="btn btn-secondary btn-custom w-100">
                    <i class="fas fa-sticky-note"></i> 
                    APP de Notas
                </a>
            </div>
            <div class="text-center">
                <a href="../UsersApp/index.php" class="btn btn-secondary btn-custom w-100">
                    <i class="fas fa-users"></i> 
                    APP de Usuarios
                </a>
            </div>
            
        </div>
    </div>

    <script src="../Framework/js/jquery-3.7.1.js"></script>
    <script src="../Framework/js/bootstrap.bundle.min.js"></script>
    <script src="../Framework/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>
