<?php
require_once __DIR__ . '/../model/repositories/MovimientoRepository.php';
// Obtener ventas diarias de los últimos 7 días
header('Content-Type: application/json');
$repo = new MovimientoRepository();
$ventas = $repo->obtenerVentasUltimosDias(7); // Devuelve ['2025-06-09'=>5, ...]
echo json_encode($ventas);
?>
