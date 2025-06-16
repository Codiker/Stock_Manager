<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
$usuario_rol = $_SESSION['usuario_rol'] ?? 0;
$titulo = "Gestión de Categorías";
require '../templates/header.php';
require '../templates/sidebar.php';
require_once __DIR__ . '../../../model/repositories/CategoriaRepository.php';

$repo = new CategoriaRepository();

$errores = [];
$success = null;

// Agregar categoría
if (isset($_POST['agregar'])) {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    if ($nombre === '') {
        $errores[] = "El nombre es obligatorio";
    }
    if (!$errores && $repo->agregar($nombre, $descripcion)) {
        $success = "Categoría agregada correctamente";
    } else if (!$errores) {
        $errores[] = "Error al agregar categoría";
    }
}

// Editar categoría
if (isset($_POST['editar'])) {
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    if ($nombre === '') {
        $errores[] = "El nombre es obligatorio";
    }
    if (!$errores && $repo->actualizar($id, $nombre, $descripcion)) {
        $success = "Categoría actualizada correctamente";
    } else if (!$errores) {
        $errores[] = "Error al actualizar categoría";
    }
}

// Desactivar categoría
if (isset($_GET['desactivar'])) {
    $id = intval($_GET['desactivar']);
    if ($repo->desactivar($id)) {
        $success = "Categoría desactivada correctamente";
    } else {
        $errores[] = "Error al desactivar categoría";
    }
}

// Activar categoría
if (isset($_GET['activar'])) {
    $id = intval($_GET['activar']);
    if ($repo->activar($id)) {
        $success = "Categoría activada correctamente";
    } else {
        $errores[] = "Error al activar categoría";
    }
}

// Obtener datos para editar
$categoriaEditar = null;
if (isset($_GET['editar'])) {
    $categoriaEditar = $repo->buscarPorId(intval($_GET['editar']));
}

$categorias = $repo->listarActivas();
$categoriasDesactivadas = $repo->listarDesactivadas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Categorías</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="assets/css/categorias.css" rel="stylesheet" />
</head>
<body>
    <div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h2>Categorías</h2>
        <!-- Botón para abrir modal de agregar -->
        <?php if ($usuario_rol == 1): ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarCategoria">
                + Nueva Categoría
            </button>
        <?php endif; ?>
    </div>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($errores): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errores as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Tabla de categorías activas -->
    <table id="tablaCategorias" class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categorias as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['nombre']) ?></td>
                    <td><?= htmlspecialchars($c['descripcion'] ?? '') ?></td>
                    <td>
                        <?php if ($usuario_rol == 1): ?>
                            <a href="?editar=<?= $c['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="?desactivar=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Desactivar esta categoría?')">Desactivar</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

    <!-- Botón para abrir modal de desactivadas -->
    <?php if ($usuario_rol == 1): ?>
        <button class="btn btn-secondary mt-3" data-bs-toggle="modal" data-bs-target="#modalDesactivadas">
            Ver categorías desactivadas
        </button>
    <?php endif; ?>
</div>

<!-- Modal Agregar Categoría -->
<div class="modal fade" id="modalAgregarCategoria" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Nombre</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Descripción</label>
                    <textarea name="descripcion" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="agregar" class="btn btn-primary">Agregar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Categoría -->
<?php if ($categoriaEditar): ?>
<div class="modal fade show" id="modalEditarCategoria" tabindex="-1" style="display:block; background:rgba(0,0,0,0.5);">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Categoría</h5>
                <a href="Categorias.php" class="btn-close"></a>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" value="<?= $categoriaEditar['id'] ?>">
                <div class="mb-3">
                    <label>Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($categoriaEditar['nombre']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Descripción</label>
                    <textarea name="descripcion" class="form-control"><?= htmlspecialchars($categoriaEditar['descripcion'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="editar" class="btn btn-primary">Actualizar</button>
                <a href="Categorias.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<script>
    // Forzar apertura del modal de edición si corresponde
    document.addEventListener('DOMContentLoaded', function() {
        var modal = new bootstrap.Modal(document.getElementById('modalEditarCategoria'));
        modal.show();
    });
</script>
<?php endif; ?>

<!-- Modal Categorías Desactivadas -->
<div class="modal fade" id="modalDesactivadas" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Categorías Desactivadas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categoriasDesactivadas as $c): ?>
                            <tr>
                                <td><?= htmlspecialchars($c['nombre']) ?></td>
                                <td><?= htmlspecialchars($c['descripcion']) ?></td>
                                <td>
                                    <a href="?activar=<?= $c['id'] ?>" class="btn btn-success btn-sm">Activar</a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    new simpleDatatables.DataTable("#tablaCategorias", {
        labels: {
            placeholder: "Buscar...",
            perPage: "Mostrar categorías por página",
            noRows: "No hay categorías para mostrar",
            info: "Mostrando {start} a {end} de {rows} categorías"
        }
    });
</script>
</body>
</html>