<?php
require_once __DIR__ . '/../model/repository/ProductRepository.php';
require_once __DIR__ . '/../model/Product.php';

$repo = new ProductRepository();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $producto = $repo->buscarPorId((int)$_GET['id']);
    if ($producto) {
        echo json_encode([
            'success' => true,
            'data' => [
                'id' => $producto->getId(),
                'nombre' => $producto->getNombre(),
                'descripcion' => $producto->getDescripcion(),
                'precio' => $producto->getPrecio(),
                'stock' => $producto->getStock(),
                'categoria_id' => $producto->getCategoriaId(),
                'activo' => $producto->isActivo(),
                'estado' => $producto->getEstado()
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $producto = new Producto(
        $id,
        $_POST['nombre'],
        $_POST['descripcion'],
        (float)$_POST['precio'],
        (int)$_POST['stock'],
        (int)$_POST['categoria_id'],
        $_POST['activo'] === '1',
        $_POST['estado']
    );

    if ($repo->guardar($producto)) {
        echo json_encode(['success' => true, 'message' => 'Producto actualizado correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
    }
    exit;
}