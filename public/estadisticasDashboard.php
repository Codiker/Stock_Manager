<?php
require_once __DIR__ . '/../model/repositories/ProductRepository.php';
require_once __DIR__ . '/../model/repositories/CategoriaRepository.php';
require_once __DIR__ . '/../model/repositories/MovimientoRepository.php';

header('Content-Type: application/json');

$productRepo = new ProductRepository();
$categoriaRepo = new CategoriaRepository();
$movimientoRepo = new MovimientoRepository(); 
 $ventasHoy = $movimientoRepo->contarVentasHoy();

echo json_encode([
    'total_categorias'        => $categoriaRepo->contarCategorias(),
    'productos_bajo_stock'    => $productRepo->contarProductosBajoStock(),
    'productos_agotados'      => $productRepo->contarProductosAgotados(),
    'ventas_hoy'              => $movimientoRepo->contarVentasHoy()
]);
   
