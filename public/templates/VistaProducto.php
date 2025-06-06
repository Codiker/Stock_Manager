<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
$usuario_rol = $_SESSION['usuario_rol'] ?? 0;
$titulo = "Gestión de Productos";
require 'header.php';
require 'sidebar.php';
require_once __DIR__ . '../../../model/repositories/ProductRepository.php';
require_once __DIR__ . '../../../model/repositories/CategoriaRepository.php';

$repo = new ProductRepository();
$categoriaRepo = new CategoriaRepository();

$productos = $repo->listarActivos(); // Solo productos activos
$categorias = $categoriaRepo->listarTodas();

$errores = [];
$valores = [];

// Registro de nuevo producto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../model/Product.php';

    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = floatval($_POST['precio'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $categoria_id = intval($_POST['categoria_id'] ?? 0);
    $activo = ($_POST['activo'] ?? '1') === '1' ? true : false;
    $estado = $_POST['estado'] ?? 'disponible';

    $valores = compact('nombre', 'descripcion', 'precio', 'stock', 'categoria_id', 'activo', 'estado');

    $producto = new Producto(
        null,
        $nombre,
        $descripcion,
        $precio,
        $stock,
        $categoria_id,
        $activo,
        $estado
    );

    $errores = $producto->validar();

    if (empty($errores)) {
        $repo->guardar($producto);
        header("Location: VistaProducto.php?success=Producto+guardado+con+éxito");
        exit();
    }
}
// Eliminación de producto
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $resultado = $repo->desactivarProducto($id);
    if ($resultado) {
        header("Location: VistaProducto.php?success=Producto+desactivado+con+éxito");
    } else {
        header("Location: VistaProducto.php?error=Error+al+desactivar+producto");
    }
    exit();
}

// Si hay errores, se muestran en el modal
if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    $_SESSION['valores'] = $valores;
    header("Location: VistaProducto.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Gestión de Productos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/alerts.js" defer></script>
    <script src="assets/js/scripts.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>



    <link href="assets/css/styles.css" rel="stylesheet" />

</head>

<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between mb-3">
            <h2>Gestión de Productos</h2>
            <?php if ($usuario_rol == 1): ?>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#nuevoProductoModal">+ Nuevo Producto</button>
            <?php endif; ?>
        </div>
        <table id="tablaProductos" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Categoría</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p->getNombre()) ?></td>
                        <td>$<?= number_format($p->getPrecio(), 2) ?></td>
                        <td><?= $p->getStock() ?></td>
                        <td><?= htmlspecialchars($p->categoria_nombre ?? 'Sin categoría') ?></td>
                        <td><?= $p->getEstado() ?></td>
                        <?php if ($usuario_rol == 1) : ?>
                            <td>
                                <button class="btn btn-primary btn-editar" data-id="<?= $p->getId() ?>">Editar</button>
                                <a href="?eliminar=<?= $p->getId() ?>" class="btn btn-danger">Eliminar</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Nuevo Producto -->
    <?php if ($usuario_rol == 1): ?>
    <div class="modal fade" id="nuevoProductoModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if (!empty($errores)): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($errores as $e): ?>
                                    <li><?= htmlspecialchars($e) ?></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif ?>

                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($valores['nombre'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Descripción</label>
                        <textarea name="descripcion" class="form-control"><?= htmlspecialchars($valores['descripcion'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Precio</label>
                        <input type="number" name="precio" class="form-control" value="<?= htmlspecialchars($valores['precio'] ?? '') ?>" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label>Stock</label>
                        <input type="number" name="stock" class="form-control" value="<?= htmlspecialchars($valores['stock'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Categoría</label>
                        <select name="categoria_id" class="form-control" required>
                            <option value="">Seleccione una categoría</option>
                            <?php foreach ($categorias as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ($valores['categoria_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['nombre']) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Activo</label>
                        <select name="activo" class="form-control">
                            <option value="1" <?= ($valores['activo'] ?? true) ? 'selected' : '' ?>>Sí</option>
                            <option value="0" <?= isset($valores['activo']) && !$valores['activo'] ? 'selected' : '' ?>>No</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Estado</label>
                        <select name="estado" class="form-control">
                            <option value="disponible">Disponible</option>
                            <option value="agotado">Agotado</option>
                            <option value="bajo">Bajo</option>
                            <option value="descontinuado">Descontinuado</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Guardar</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($usuario_rol == 1): ?>
    <!-- Modal Edición (dinámico con JS + fetch) -->
    <div class="modal fade" id="modalEditarProducto" tabindex="-1">
        <div class="modal-dialog">
            <form id="formEditarProducto" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" name="nombre" id="edit-nombre" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Descripción</label>
                        <textarea name="descripcion" id="edit-descripcion" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Precio</label>
                        <input type="number" name="precio" id="edit-precio" class="form-control" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label>Stock</label>
                        <input type="number" name="stock" id="edit-stock" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Categoría</label>
                        <select name="categoria_id" id="edit-categoria_id" class="form-control">
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Activo</label>
                        <select name="activo" id="edit-activo" class="form-control">
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Estado</label>
                        <select name="estado" id="edit-estado" class="form-control">
                            <option value="disponible">Disponible</option>
                            <option value="agotado">Agotado</option>
                            <option value="bajo">Bajo</option>
                            <option value="descontinuado">Descontinuado</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">Guardar cambios</button>
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script de edición -->
    <script>
        document.querySelectorAll('.btn-editar').forEach(btn => {
            btn.addEventListener('click', async function() {
                const id = this.dataset.id;
                const resp = await fetch('/../controller/productController.php?id=' + id);
                const data = await resp.json();
                if (data.error) {
                    alert(data.error);
                    return;
                }
                document.getElementById('edit-id').value = data.id;
                document.getElementById('edit-nombre').value = data.nombre;
                document.getElementById('edit-descripcion').value = data.descripcion;
                document.getElementById('edit-precio').value = data.precio;
                document.getElementById('edit-stock').value = data.stock;
                document.getElementById('edit-categoria_id').value = data.categoria_id;
                document.getElementById('edit-activo').value = data.activo ? '1' : '0';
                document.getElementById('edit-estado').value = data.estado;
                var modal = new bootstrap.Modal(document.getElementById('modalEditarProducto'));
                modal.show();
            });
        });

        document.getElementById('formEditarProducto').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const datos = {
                id: form['id'].value,
                nombre: form['nombre'].value,
                descripcion: form['descripcion'].value,
                precio: form['precio'].value,
                stock: form['stock'].value,
                categoria_id: form['categoria_id'].value,
                activo: form['activo'].value,
                estado: form['estado'].value
            };
            const resp = await fetch('updateProducto.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datos)
            });
            const data = await resp.json();
            if (data.success) {
                location.reload(); // O actualizar solo la fila si prefieres
            } else {
                alert(data.error ? data.error : 'Error al actualizar');
            }
        });
    </script>
    <?php endif; ?>

    <!-- Script de tabla -->
    <script>
        const datatable = new simpleDatatables.DataTable("#tablaProductos");
    </script>

</body>

</html>