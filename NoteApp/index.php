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
    <link rel="stylesheet" href="../Framework/custom/noteapp.css">
    <link rel="stylesheet" href="../Framework/custom/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Administrador de notas</title>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Aplicacion de Notas</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="../Login/View/ProfileView.php">Mi Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="../welcome/index.php">Volver al Panel</a>
                        </li>
                    </ul>
                    <span class="navbar-text me-3">
                        Bienvenido, <?php echo htmlspecialchars($user['username']); ?>
                    </span>
                    <a href="../Login/Logout/exit.php" class="btn btn-outline-danger">Cerrar Sesión</a>
                </div>
            </div>
        </nav>
    </header>

    <div class="container mt-5">
        <h3 class="mb-4">Estas en : Gestión de notas</h3>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Notas</h5>
                        <p class="card-text">Gestiona las notas</p>
                        <a href="View/NoteListView.php" class="btn btn-primary"><i class="fas fa-sticky-note"></i> Ver todas las Notas</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Mis Notas</h5>
                        <p class="card-text">Gestiona tus notas</p>
                        <a href="View/NoteMyView.php" class="btn btn-warning"><i class="fas fa-eye"></i> Ver Mis Notas</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Añadir Notas</h5>
                        <p class="card-text">Añade nuevas notas</p>
                        <a href="View/NoteCreateView.php" class="btn btn-success"><i class="fas fa-plus"></i> Añadir Notas</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Asignaciones</h5>
                        <p class="card-text">Asigna notas a tus usuarios</p>
                        <a href="View/AssignNotesView.php" class="btn btn-assign-notes">
                            <i class="fas fa-user-plus"></i> Asignar Notas
                        </a>
                    </div>
                </div>
            </div>


            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Reportes</h5>
                        <p class="card-text">Genera reportes de tus notas</p>
                        <a href="View/NoteReportView.php" class="btn btn-info"><i class="fas fa-chart-line"></i> Ver Reportes</a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="../Framework/js/jquery-3.7.1.js"></script>
    <script src="../Framework/js/bootstrap.bundle.min.js"></script>
    <script src="../Framework/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

</body>

</html>