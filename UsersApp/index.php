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
     <link rel="stylesheet" href="../Framework/css/bootstrap.css">
     <link rel="stylesheet" href="../Framework/custom/assignnotes.css">
     <link rel="stylesheet" href="../Framework/custom/menu.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
     <title>APP de usuarios</title>
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

     <div class="container mt-5">
        <h3 class="mb-4">Estas en : Gestión de Usuarios</h3>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Usuarios</h5>
                        <p class="card-text">Gestiona los Usuarios</p>
                        <a href="View/UsersListView.php" class="btn btn-primary"><i class="fas fa-sticky-note"></i> Ver todos las Usuarios</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Añadir Usuarios</h5>
                        <p class="card-text">Añade nuevos Usuarios</p>
                        <a href="View/UsersCreateView.php" class="btn btn-success"><i class="fas fa-plus"></i> Añadir Usuarios</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Grafica</h5>
                        <p class="card-text">Genera Grafica de tus Usuarios</p>
                        <a href="View/graficadeusuarios.php" class="btn btn-info"><i class="fas fa-chart-line"></i> Ver Graficas</a>
                    </div>
                </div>
            </div>

        </div>
    </div>

     <script src="../Framework/js/jquery-3.7.1.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
     <script src="../Framework/js/bootstrap.bundle.min.js"></script>
     <script src="../Framework/js/bootstrap.min.js"></script>
 </body>

 </html>