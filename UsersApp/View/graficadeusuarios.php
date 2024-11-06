<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit();
}
$user = $_SESSION['user'];

require_once '../Controller/GraficaController.php';

$graficasController = new GraficaUsuarios();
$datosGrafica = $graficasController->obtenerDatosGrafica();
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
    <title>Grafica de usuarios</title>
    <style>
        #graficaUsuarios {
            width: 700px !important;
            height: 700px !important;
        }
        .chartjs-render-monitor {
            position: relative;
        }
        .username-label {
            position: absolute;
            color: white;
            background-color: black;
            padding: 2px 5px;
            transform: translate(-50%, -100%);
            white-space: nowrap;
            z-index: 10;
            font-size: 12px;
            text-align: center;
            border-radius: 3px;
        }
        .username-label::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: black transparent transparent transparent;
        }
    </style>
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
                <h3>Grafica de Usuarios</h3>
                <canvas id="graficaUsuarios"></canvas>
            </div>
        </div>
    </div>

    <script src="../../Framework/js/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
    <script src="../../Framework/js/bootstrap.bundle.min.js"></script>
    <script src="../../Framework/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var datosGrafica = <?php echo json_encode($datosGrafica); ?>;
            var labels = datosGrafica.map(function(item) {
                return item.username;
            });
            var data = datosGrafica.map(function(item) {
                return item.total_veces_on;
            });

            var ctx = document.getElementById('graficaUsuarios').getContext('2d');
            ctx.canvas.width = 700;
            ctx.canvas.height = 700;

            var colors = [
                'rgba(75, 192, 192, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(153, 102, 255, 0.8)',
                'rgba(255, 159, 64, 0.8)',
                'rgba(255, 99, 132, 0.8)'
            ];

            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Conexiones',
                        data: data,
                        backgroundColor: colors,
                        borderColor: colors.map(color => color.replace('0.8', '1')),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Cantidad de veces que los usuarios se han conectado en el mes'
                        },
                        datalabels: {
                            anchor: 'center',
                            align: 'center',
                            color: '#fff',
                            formatter: (value, ctx) => {
                                return 'Conectado ' + value + ' veces';
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });

            myChart.options.animation.onComplete = function() {
                var chartInstance = myChart;
                var ctx = chartInstance.ctx;
                var datasets = chartInstance.data.datasets;

                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.font = '12px Arial';

                datasets.forEach(function(dataset, i) {
                    var meta = chartInstance.getDatasetMeta(i);
                    meta.data.forEach(function(bar, index) {
                        var data = dataset.data[index];
                        var model = bar._model;
                        var midX = model.x;
                        var midY = model.y + (model.height / 2);

                        var username = chartInstance.data.labels[index];
                        var label = document.createElement('div');
                        label.classList.add('username-label');
                        label.style.left = model.x + 'px';
                        label.style.top = model.y + 'px';
                        label.innerHTML = username;
                        document.querySelector('.chartjs-render-monitor').appendChild(label);
                    });
                });
            };
        });
    </script>
</body>
</html>
