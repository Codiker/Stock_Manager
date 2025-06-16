<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 1) {
    header("Location: login.php");
    exit();
}
require_once __DIR__ . '../../../model/repositories/UsuarioRepository.php';
require '../templates/header.php';
require '../templates/sidebar.php';

$repo = new UsuarioRepository();
$errores = [];
$success = null;

// Desactivar usuario
if (isset($_GET['desactivar'])) {
    $id = intval($_GET['desactivar']);
    if ($repo->actualizarEstado($id, 0)) {
        $success = "Usuario desactivado correctamente";
    } else {
        $errores[] = "Error al desactivar usuario";
    }
}

// Activar usuario
if (isset($_GET['activar'])) {
    $id = intval($_GET['activar']);
    if ($repo->actualizarEstado($id, 1)) {
        $success = "Usuario activado correctamente";
    } else {
        $errores[] = "Error al activar usuario";
    }
}

// Editar usuario (solo nombre, rol y estado)
if (isset($_POST['editar'])) {
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $rol_id = intval($_POST['rol_id']);
    $estado = intval($_POST['estado']);
    if ($nombre === '') {
        $errores[] = "El nombre es obligatorio";
    }
    if (!$errores && $repo->actualizarDatos($id, $nombre, $rol_id, $estado)) {
        $success = "Usuario actualizado correctamente";
    } else if (!$errores) {
        $errores[] = "Error al actualizar usuario";
    }
}

// Obtener datos para editar
$usuarioEditar = null;
if (isset($_GET['editar'])) {
    $usuarioEditar = $repo->buscarPorId(intval($_GET['editar']));
}

// Listar todos los usuarios
$usuarios = $repo->listarTodos();
?>
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h2>Usuarios</h2>
        <a href="register.php" class="btn btn-primary">+ Nuevo Usuario</a>
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

    <!-- Tabla de usuarios -->
    <table id="tablaUsuarios" class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['id']) ?></td>
                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= $u['rol_id'] == 1 ? 'Administrador' : 'Usuario' ?></td>
                    <td>
                        <?php if ($u['estado'] == 1): ?>
                            <span class="badge bg-success">Activo</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?editar=<?= $u['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                        <?php if ($u['estado'] == 1 && $u['id'] != $_SESSION['usuario_id']): ?>
                            <a href="?desactivar=<?= $u['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Desactivar este usuario?')">Desactivar</a>
                        <?php elseif ($u['estado'] == 0): ?>
                            <a href="?activar=<?= $u['id'] ?>" class="btn btn-success btn-sm">Activar</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<!-- Modal Editar Usuario -->
<?php if ($usuarioEditar): ?>
<div class="modal fade show" id="modalEditarUsuario" tabindex="-1" style="display:block; background:rgba(0,0,0,0.5);">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Usuario</h5>
                <a href="usuarios.php" class="btn-close"></a>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" value="<?= $usuarioEditar['id'] ?>">
                <div class="mb-3">
                    <label>Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuarioEditar['nombre']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Rol</label>
                    <select name="rol_id" class="form-control">
                        <option value="1" <?= $usuarioEditar['rol_id'] == 1 ? 'selected' : '' ?>>Administrador</option>
                        <option value="2" <?= $usuarioEditar['rol_id'] == 2 ? 'selected' : '' ?>>Usuario</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Estado</label>
                    <select name="estado" class="form-control">
                        <option value="1" <?= $usuarioEditar['estado'] == 1 ? 'selected' : '' ?>>Activo</option>
                        <option value="0" <?= $usuarioEditar['estado'] == 0 ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" class="form-control" value="<?= htmlspecialchars($usuarioEditar['email']) ?>" disabled>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="editar" class="btn btn-primary">Actualizar</button>
                <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
        modal.show();
    });
</script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    new simpleDatatables.DataTable("#tablaUsuarios", {
        labels: {
            placeholder: "Buscar...",
            perPage: "Mostrar usuarios por página",
            noRows: "No hay usuarios para mostrar",
            info: "Mostrando {start} a {end} de {rows} usuarios"
        }
    });
</script>
</body>
</html>