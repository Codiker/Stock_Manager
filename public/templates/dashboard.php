<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
$usuario_rol = $_SESSION['usuario_rol'] ?? 0; // 1: Admin, 2: Usuario común
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panel de Control - StockManager</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="assets/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <!-- BARRA SUPERIOR -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="#">StockManager</a>
        <button class="btn btn-link btn-sm me-4" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        <ul class="navbar-nav ms-auto me-3">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" data-bs-toggle="dropdown">
                    <i class="fas fa-user fa-fw"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">Perfil</a></li>
                    <li><a class="dropdown-item" href="logout.php">Cerrar sesión</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <!-- LAYOUT -->
    <div id="layoutSidenav">
        <!-- MENÚ LATERAL -->
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">General</div>
                        <a class="nav-link" href="dashboard.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div> Dashboard
                        </a>

                        <div class="sb-sidenav-menu-heading">Gestión</div>
                        <a class="nav-link" href="VistaProducto.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-boxes"></i></div> Productos
                        </a>
                        <a class="nav-link" href="categorias.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tags"></i></div> Categorías
                        </a>
                        <a class="nav-link" href="reportes.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div> Reportes
                        </a>
                        <?php if ($usuario_rol == 1): ?>
                        <a class="nav-link" href="usuarios.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div> Usuarios
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Conectado como:</div>
                   <?php if ($usuario_rol == 1) {
                        echo '<span class="text-success">Administrador</span>';
                    } elseif ($usuario_rol == 2) {
                        echo '<span class="text-info">Usuario</span>';
                    } else {
                        echo '<span class="text-secondary">Invitado</span>';
                    } ?>
                    <?= $_SESSION['usuario_nombre'] ?? 'Usuario' ?>
                </div>
            </nav>
        </div>

        <!-- CONTENIDO PRINCIPAL -->
        <div id="layoutSidenav_content">
            <main class="container-fluid px-4">
                <h1 class="mt-4">Panel de Control</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Bienvenido al sistema</li>
                </ol>

                <!-- RESUMEN RÁPIDO -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white mb-4">
                            <div class="card-body">Categorías registradas</div>
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <a class="small text-white stretched-link" href="categorias.php">Ver más</a>
                                <div><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white mb-4">
                            <div class="card-body">Stock Bajo (ej: 10)</div>
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <a class="small text-white stretched-link" href="#">Detalles</a>
                                <div><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white mb-4">
                            <div class="card-body">Ventas hoy (ej: $150.000)</div>
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <a class="small text-white stretched-link" href="#">Detalles</a>
                                <div><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white mb-4">
                            <div class="card-body">Productos agotados</div>
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <a class="small text-white stretched-link" href="#">Detalles</a>
                                <div><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GRÁFICOS -->
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card mb-4">
                            <div class="card-header"><i class="fas fa-chart-area me-1"></i> Ventas Diarias</div>
                            <div class="card-body"><canvas id="myAreaChart" width="100%" height="40"></canvas></div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card mb-4">
                            <div class="card-header"><i class="fas fa-chart-bar me-1"></i> Ventas últimos 6 meses</div>
                            <div class="card-body"><canvas id="myBarChart" width="100%" height="40"></canvas></div>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid text-center">
                    <div class="text-muted">© <?= date('Y') ?> StockManager</div>
                </div>
            </footer>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/chart-area-demo.js"></script>
    <script src="assets/js/chart-bar-demo.js"></script>
</body>
</html>
