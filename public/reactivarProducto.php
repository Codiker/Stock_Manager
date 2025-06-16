<?php
require_once __DIR__ . '/../model/repositories/ProductRepository.php';

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'error' => 'ID requerido']);
    exit;
}

$repo = new ProductRepository();
$producto = $repo->buscarPorId((int)$data['id']);
if (!$producto) {
    echo json_encode(['success' => false, 'error' => 'Producto no encontrado']);
    exit;
}

$producto->setActivo(true);
$result = $repo->guardar($producto);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'No se pudo reactivar']);
}