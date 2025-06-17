<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
$usuario_rol = $_SESSION['usuario_rol'] ?? 0;
require 'header.php';
require 'sidebar.php';
require_once __DIR__ . '../../../model/repositories/ProductRepository.php';
require_once __DIR__ . '../../../model/repositories/MovimientoRepository.php';

$productRepo = new ProductRepository();
$movimientoRepo = new MovimientoRepository();

$productos = $productRepo->listarActivos();
$mensaje = $error = null;

// Procesar venta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto_id = intval($_POST['producto_id'] ?? 0);
    $cantidad = intval($_POST['cantidad'] ?? 0);

    $producto = $productRepo->buscarPorId($producto_id);

    if (!$producto) {
        $error = "Producto no encontrado.";
    } elseif ($cantidad <= 0) {
        $error = "La cantidad debe ser mayor a cero.";
    } elseif ($producto->getStock() < $cantidad) {
        $error = "Stock insuficiente para la venta.";
    } else {
        // Descontar stock
        $producto->setStock($producto->getStock() - $cantidad);
        $productRepo->guardar($producto);

        // Registrar movimiento de venta
        require_once __DIR__ . '/../../model/Movimiento.php';
        $movimiento = new Movimiento(
            null,
            $producto_id,
            $cantidad,
            'salida',
            date('Y-m-d H:i:s'),
            true, 
            date('Y-m-d H:i:s'),
            null
        );
        $movimientoRepo->guardar($movimiento);

        $mensaje = "Venta registrada correctamente.";
        // Refrescar productos para mostrar stock actualizado
        $productos = $productRepo->listarActivos();
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registrar Venta - StockManager</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="assets/css/VistaProducto.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container mt-5">
        <h2>Registrar Venta</h2>
        <?php if ($mensaje): ?>
            <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Producto</label>
                    <select name="producto_id" class="form-control" required>
                        <option value="">Seleccione un producto</option>
                        <?php foreach ($productos as $p): ?>
                            <option value="<?= $p->getId() ?>">
                                <?= htmlspecialchars($p->getNombre()) ?> (Stock: <?= $p->getStock() ?>)
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Cantidad</label>
                    <input type="number" name="cantidad" class="form-control" min="1" required>
                </div>
                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Vender</button>
                </div>
            </div>
        </form>

        <h4 class="mt-5">Historial de Ventas Recientes</h4>
        <table id="tablaVentas" class="table table-bordered">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Mostrar los últimos 10 movimientos de tipo venta
                $ventas = $movimientoRepo->listarTodos();
                $ventas = array_filter($ventas, fn($m) => strtolower(trim($m->getTipo())) === 'salida');
                $ventas = array_slice($ventas, 0, 10);
                foreach ($ventas as $v): ?>
                    <tr>
                        <td><?= htmlspecialchars($v->getFecha()) ?></td>
                        <td><?= htmlspecialchars($v->getProductoNombre()) ?></td>
                        <td><?= $v->getCantidad() ?></td>
                        <td><?= $v->getEstado() ? 'Completado' : 'Anulado' ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
    <script>
        new simpleDatatables.DataTable("#tablaVentas", {
            labels: {
                placeholder: "Buscar ventas...",
                perPage: "ventas por página",
                noRows: "No se encontraron ventas",
                info: "Mostrando {start} a {end} de {rows} ventas"
            }
        });
    </script>
</body>

</html>