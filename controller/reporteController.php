<?php
require_once __DIR__ . '/../model/repositories/ProductRepository.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReporteController {
    private $productRepo;

    public function __construct() {
        $this->productRepo = new ProductRepository();
    }

    public function generarReporteInventario(string $tipo = 'todos'): ?string {
        try {
            // Obtener productos según el tipo de reporte
            $productos = match($tipo) {
                'activos' => $this->productRepo->listarActivos(),
                'agotados' => $this->productRepo->obtenerProductosAgotados(),
                'bajo_stock' => $this->productRepo->obtenerProductosBajoStock(),
                'todos' => $this->productRepo->listarTodos(),
                default => throw new \InvalidArgumentException('Tipo de reporte inválido')
            };

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Configurar título
            $titulos = [
                'todos' => 'Reporte Completo de Inventario',
                'activos' => 'Reporte de Productos Activos',
                'agotados' => 'Reporte de Productos Agotados',
                'bajo_stock' => 'Reporte de Productos con Bajo Stock'
            ];
            
            $sheet->mergeCells('A1:H1');
            $sheet->setCellValue('A1', $titulos[$tipo]);
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

            // Encabezados
            $encabezados = [
                'A2' => 'ID',
                'B2' => 'Nombre',
                'C2' => 'Descripción',
                'D2' => 'Precio',
                'E2' => 'Stock',
                'F2' => 'Categoría',
                'G2' => 'Estado',
                'H2' => 'Fecha Actualización'
            ];

            foreach ($encabezados as $celda => $valor) {
                $sheet->setCellValue($celda, $valor);
                $sheet->getStyle($celda)->getFont()->setBold(true);
            }

            // Datos
            $row = 3;
            foreach ($productos as $producto) {
                $sheet->setCellValue('A' . $row, $producto->getId());
                $sheet->setCellValue('B' . $row, $producto->getNombre());
                $sheet->setCellValue('C' . $row, $producto->getDescripcion());
                $sheet->setCellValue('D' . $row, $producto->getPrecio());
                $sheet->setCellValue('E' . $row, $producto->getStock());
                $sheet->setCellValue('F' . $row, $producto->categoria_nombre);
                $sheet->setCellValue('G' . $row, $producto->getEstado());
                $sheet->setCellValue('H' . $row, $producto->getUpdatedAt() ?? $producto->getCreatedAt());
                $row++;
            }

            // Autoajustar columnas
            foreach(range('A','H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Modificar la ruta y el manejo de directorios
            $baseDir = dirname(__DIR__);
            $reportDir = $baseDir . '/public/reportes';
            
            // Asegurar que el directorio existe
            if (!file_exists($reportDir)) {
                if (!mkdir($reportDir, 0777, true)) {
                    throw new \RuntimeException('No se pudo crear el directorio de reportes');
                }
            }

            // Generar nombre único para el archivo
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "reporte_{$tipo}_{$timestamp}.xlsx";
            $filepath = $reportDir . '/' . $filename;
            
            // Guardar archivo
            $writer = new Xlsx($spreadsheet);
            $writer->save($filepath);
            
            // Verificar que el archivo se creó correctamente
            if (!file_exists($filepath)) {
                throw new \RuntimeException('El archivo no se guardó correctamente');
            }

            return $filename;

        } catch (\Exception $e) {
            error_log("Error al generar reporte: " . $e->getMessage());
            return null;
        }
    }

    public function obtenerEstadisticas(): array {
        return [
            'total_productos' => $this->productRepo->contarTotalProductos(),
            'productos_agotados' => $this->productRepo->contarProductosAgotados(),
            'productos_bajo_stock' => $this->productRepo->contarProductosBajoStock(),
            'valor_total_inventario' => $this->productRepo->calcularValorTotalInventario()
        ];
    }
}
