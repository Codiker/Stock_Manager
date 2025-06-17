<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav" id="sidenavAccordion">
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
                
                <a class="nav-link" href="VistaVentas.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-cash-register"></i></div> Ventas
                </a>

                <?php if ($_SESSION['usuario_rol'] === 1): ?>
                    <a class="nav-link" href="usuarios.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div> Usuarios
                    </a>
                <?php endif; ?>
                <a class="nav-link text-danger" href="logout.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div> Salir
                </a>
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Conectado como:</div>
            <?php
            if ($_SESSION['usuario_rol'] === 1) {
                echo '<span class="text-success">Administrador</span>';
            } elseif ($_SESSION['usuario_rol'] === 2) {
                echo '<span class="text-info">Usuario</span>';
            } else {
                echo '<span class="text-secondary">Invitado</span>';
            }
            ?>
            <?= $_SESSION['usuario_nombre'] ?? 'Usuario' ?>
        </div>
    </nav>
</div>