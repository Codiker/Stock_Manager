<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../../controller/reporteController.php';
$reporteController = new ReporteController();
$estadisticas = $reporteController->obtenerEstadisticas();

// Generar reporte si se solicita
if (isset($_POST['generar_reporte'])) {
    $tipo = $_POST['tipo_reporte'] ?? 'todos';
    $filename = $reporteController->generarReporteInventario($tipo);
    if ($filename) {
        // Usar ruta relativa desde la ubicación actual
        $downloadUrl = "../reportes/" . $filename;
    } else {
        $error = "Error al generar el reporte";
    }
}

// Generar PDF si se solicita
if (isset($_POST['generar_pdf'])) {
    require_once __DIR__ . '/../../controller/reportePDFController.php';
    $pdfController = new ReportePDFController();
    $pdfFile = $pdfController->generarMovimientosPDF();
    if ($pdfFile) {
        $pdfDownloadUrl = "../reportes/" . htmlspecialchars($pdfFile);
    } else {
        $pdfError = "Error al generar el PDF";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes - StockManager</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="assets/css/reportes.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="sb-nav-fixed">
    <?php include 'header.php'; ?>
    <div id="layoutSidenav">
        <?php include 'sidebar.php'; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Reportes</h1>
                    
                    <!-- Tarjetas de Estadísticas -->
                    <div class="row mt-4">
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">
                                    <h2><?= $estadisticas['total_productos'] ?></h2>
                                    <p>Total Productos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-warning text-white mb-4">
                                <div class="card-body">
                                    <h2><?= $estadisticas['productos_bajo_stock'] ?></h2>
                                    <p>Productos Bajo Stock</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-danger text-white mb-4">
                                <div class="card-body">
                                    <h2><?= $estadisticas['productos_agotados'] ?></h2>
                                    <p>Productos Agotados</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">
                                    <h2>$<?= number_format($estadisticas['valor_total_inventario'], 2) ?></h2>
                                    <p>Valor Total Inventario</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acciones -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Generar Reportes</h5>
                                    <form method="POST" class="mt-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <select name="tipo_reporte" class="form-select mb-3">
                                                    <option value="todos">Todos los productos</option>
                                                    <option value="activos">Productos activos</option>
                                                    <option value="agotados">Productos agotados</option>
                                                    <option value="bajo_stock">Productos con bajo stock</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="submit" name="generar_reporte" class="btn btn-primary">
                                                    <i class="fas fa-file-excel me-2"></i>Exportar a Excel
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <?php if (isset($downloadUrl)): ?>
                                        <div class="alert alert-success mt-3">
                                            Reporte generado exitosamente. 
                                            <a href="<?= htmlspecialchars($downloadUrl) ?>" class="alert-link" id="downloadLink">
                                                Descargar archivo
                                            </a>
                                        </div>
                                    <?php elseif (isset($error)): ?>
                                        <div class="alert alert-danger mt-3">
                                            <?= $error ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Formulario para generar PDF -->
                                    <form method="POST" class="mt-3">
                                        <button type="submit" name="generar_pdf" class="btn btn-danger">
                                            <i class="fas fa-file-pdf me-2"></i>Exportar Movimientos a PDF
                                        </button>
                                    </form>
                                    <?php if (isset($pdfDownloadUrl)): ?>
                                        <div class="alert alert-success mt-3">
                                            PDF generado exitosamente.
                                            <a href="<?= $pdfDownloadUrl ?>" target="_blank">Descargar PDF</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/scripts.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const downloadLink = document.getElementById('downloadLink');
        if (downloadLink) {
            downloadLink.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.href;
                
                // Verificar que el archivo existe antes de intentar descargarlo
                fetch(url, { method: 'HEAD' })
                    .then(response => {
                        if (response.ok) {
                            window.location.href = url;
                        } else {
                            throw new Error('Archivo no encontrado');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al descargar el archivo: ' + error.message);
                    });
            });
        }
    });
    </script>
</body>
</html>
