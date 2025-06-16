<?php
require_once __DIR__ . '/../model/repositories/ProductRepository.php';
header('Content-Type: application/json');
$repo = new ProductRepository();
$data = $repo->contarProductosPorCategoria();
echo json_encode($data);