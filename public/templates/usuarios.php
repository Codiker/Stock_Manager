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

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Gestión de Usuarios | Sistema de Inventario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap-grid.min.css" rel="stylesheet" />
    <link href="assets/css/usuarios.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">
</head>

<body>
    <!-- Contenido principal -->
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">
                <i class="fas fa-users-cog me-2"></i>Gestión de Usuarios
            </h1>
            <a href="register.php" class="btn btn-primary">
                <i class="fas fa-user-plus me-1"></i> Nuevo Usuario
            </a>
        </div>

        <!-- Alertas -->
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($errores): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <ul class="mb-0">
                    <?php foreach ($errores as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Tabla de usuarios -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table id="tablaUsuarios" class="table table-hover mb-0">
                    <thead class="table-header">
                        <tr>
                            <th><i class="fas fa-id-card me-1"></i> ID</th>
                            <th><i class="fas fa-user me-1"></i> Nombre</th>
                            <th><i class="fas fa-envelope me-1"></i> Email</th>
                            <th><i class="fas fa-user-tag me-1"></i> Rol</th>
                            <th><i class="fas fa-circle me-1"></i> Estado</th>
                            <th><i class="fas fa-cog me-1"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><?= htmlspecialchars($u['id']) ?></td>
                                <td><?= htmlspecialchars($u['nombre']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td>
                                    <?php if ($u['rol_id'] == 1): ?>
                                        <span class="badge bg-admin">
                                            <i class="fas fa-shield-alt me-1"></i> Administrador
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-user">
                                            <i class="fas fa-user me-1"></i> Usuario
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($u['estado'] == 1): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i> Activo
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle me-1"></i> Inactivo
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="action-buttons">
                                    <a href="?editar=<?= $u['id'] ?>" class="btn btn-warning btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($u['estado'] == 1 && $u['id'] != $_SESSION['usuario_id']): ?>
                                        <a href="?desactivar=<?= $u['id'] ?>" class="btn btn-danger btn-sm" title="Desactivar" onclick="return confirm('¿Desactivar este usuario?')">
                                            <i class="fas fa-user-slash"></i>
                                        </a>
                                    <?php elseif ($u['estado'] == 0): ?>
                                        <a href="?activar=<?= $u['id'] ?>" class="btn btn-success btn-sm" title="Activar">
                                            <i class="fas fa-user-check"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Editar Usuario -->
    <?php if ($usuarioEditar): ?>
    <div class="modal-backdrop fade show" id="modalBackdrop"></div>
    <div class="modal fade show d-block" id="modalEditarUsuario" tabindex="-1" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-edit me-2"></i>Editar Usuario
                    </h5>
                    <a href="usuarios.php" class="btn-close" aria-label="Close"></a>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $usuarioEditar['id'] ?>">
                    <div class="mb-3 form-group">
                        <label class="form-label"><i class="fas fa-signature me-1"></i> Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuarioEditar['nombre']) ?>" required>
                    </div>
                    <div class="mb-3 form-group">
                        <label class="form-label"><i class="fas fa-user-tag me-1"></i> Rol</label>
                        <select name="rol_id" class="form-select">
                            <option value="1" <?= $usuarioEditar['rol_id'] == 1 ? 'selected' : '' ?>>
                                <i class="fas fa-shield-alt me-1"></i> Administrador
                            </option>
                            <option value="2" <?= $usuarioEditar['rol_id'] == 2 ? 'selected' : '' ?>>
                                <i class="fas fa-user me-1"></i> Usuario
                            </option>
                        </select>
                    </div>
                    <div class="mb-3 form-group">
                        <label class="form-label"><i class="fas fa-power-off me-1"></i> Estado</label>
                        <select name="estado" class="form-select">
                            <option value="1" <?= $usuarioEditar['estado'] == 1 ? 'selected' : '' ?>>
                                <i class="fas fa-check-circle me-1"></i> Activo
                            </option>
                            <option value="0" <?= $usuarioEditar['estado'] == 0 ? 'selected' : '' ?>>
                                <i class="fas fa-times-circle me-1"></i> Inactivo
                            </option>
                        </select>
                    </div>
                    <div class="mb-3 form-group">
                        <label class="form-label"><i class="fas fa-envelope me-1"></i> Email</label>
                        <input type="email" class="form-control" value="<?= htmlspecialchars($usuarioEditar['email']) ?>" disabled>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="editar" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Actualizar
                    </button>
                    <a href="usuarios.php" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/alerts.js" defer></script>
    <script src="assets/js/usuarios.js" defer></script>

    <script>
        // Inicialización de DataTable
        document.addEventListener('DOMContentLoaded', function() {
            new simpleDatatables.DataTable("#tablaUsuarios", {
                labels: {
                    placeholder: "Buscar usuarios...",
                    perPage: "usuarios por página",
                    noRows: "No se encontraron usuarios",
                    info: "Mostrando {start} a {end} de {rows} usuarios"
                },
                columns: [
                    { select: 0, sort: "asc" }, // Ordenar por ID ascendente
                    { select: 1, sortable: true }, // Nombre
                    { select: 2, sortable: true }, // Email
                    { select: 3, sortable: false }, // Rol
                    { select: 4, sortable: false }, // Estado
                    { select: 5, sortable: false }  // Acciones
                ]
            });

            <?php if ($usuarioEditar): ?>
                // Mostrar modal de edición
                const modal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
                modal.show();
            <?php endif; ?>
        });
    </script>
</body>
</html>