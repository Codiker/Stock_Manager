<?php ?>
<aside class="bg-light border-end" style="width:220px; min-height:100vh;">
    <div class="list-group list-group-flush">
        <a href="dashboard.php" class="list-group-item list-group-item-action">Dashboard</a>
        <a href="VistaProducto.php" class="list-group-item list-group-item-action">Productos</a>
        <a href="categorias.php" class="list-group-item list-group-item-action">Categorías</a>
        <div>
            <a href="categorias.php" class="list-group-item list-group-item-action">Reportes</a>
        </div>
        <!-- Opciones por rol -->
        <?php if ($_SESSION['rol'] === 'admin'): ?>
            <a href="usuarios.php" class="list-group-item list-group-item-action">Usuarios</a>
        <?php endif ?>
        <a href="logout.php" class="list-group-item list-group-item-action text-danger">Salir</a>
    </div>
</aside>
<div class="flex-grow-1 p-4">