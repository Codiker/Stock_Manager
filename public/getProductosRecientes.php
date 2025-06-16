<?php
require_once __DIR__ . '/../model/repositories/ProductRepository.php';

header('Content-Type: application/json');

$repo = new ProductRepository();
$productos = $repo->listarTodos(10, 0);

$result = [];
foreach ($productos as $p) {
    $result[] = [
        'nombre' => $p->getNombre(),
        'precio' => $p->getPrecio(),
        'stock' => $p->getStock(),
        'categoria' => $p->categoria_nombre ?? 'Sin categoría',
        'fecha' => $p->getCreatedAt()
    ];
}
echo json_encode($result);