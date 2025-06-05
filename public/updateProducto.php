<?php
require_once __DIR__ . '/../model/repositories/ProductRepository.php';
require_once __DIR__ . '/../model/Product.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos inválidos']);
    exit;
}

$repo = new ProductRepository();
$producto = $repo->buscarPorId((int)$data['id']);

if (!$producto) {
    http_response_code(404);
    echo json_encode(['error' => 'Producto no encontrado']);
    exit;
}

// Actualizar propiedades
$producto->setNombre($data['nombre'] ?? '');
$producto->setDescripcion($data['descripcion'] ?? '');
$producto->setPrecio(floatval($data['precio'] ?? 0));
$producto->setStock(intval($data['stock'] ?? 0));
$producto->setCategoriaId(intval($data['categoria_id'] ?? 0));
$producto->setActivo(($data['activo'] ?? true) ? true : false);
$producto->setEstado($data['estado'] ?? 'disponible');

$errores = $producto->validar();
if (!empty($errores)) {
    http_response_code(422);
    echo json_encode(['error' => $errores]);
    exit;
}

$result = $repo->guardar($producto);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al actualizar']);
}