<?php
require_once __DIR__ . '/../model/repositories/ProductRepository.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID requerido']);
    exit;
}

$id = intval($_GET['id']);
$repo = new ProductRepository();
$producto = $repo->buscarPorId($id);

if (!$producto) {
    http_response_code(404);
    echo json_encode(['error' => 'Producto no encontrado']);
    exit;
}

echo json_encode([
    'id' => $producto->getId(),
    'nombre' => $producto->getNombre(),
    'descripcion' => $producto->getDescripcion(),
    'precio' => $producto->getPrecio(),
    'stock' => $producto->getStock(),
    'categoria_id' => $producto->getCategoriaId(),
    'activo' => $producto->isActivo(),
    'estado' => $producto->getEstado()
]);