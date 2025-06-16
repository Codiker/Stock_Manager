<?php
require_once __DIR__ . '/../model/repositories/ProductRepository.php';
require_once __DIR__ . '/../model/repositories/CategoriaRepository.php';

header('Content-Type: application/json');

$productRepo = new ProductRepository();
$categoriaRepo = new CategoriaRepository();

echo json_encode([
    'total_categorias'        => $categoriaRepo->contarCategorias(),
    'productos_bajo_stock'    => $productRepo->contarProductosBajoStock(),
    'productos_agotados'      => $productRepo->contarProductosAgotados(),
]);