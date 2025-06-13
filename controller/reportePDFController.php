<?php
require_once __DIR__ . '/../model/repositories/MovimientoRepository.php';

class ReportePDFController
{
    public function generarMovimientosPDF()
    {

        $repo = new MovimientoRepository();
        $movimientos = $repo->listarTodos();

        $pdf = new \TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('StockManager');
        $pdf->SetTitle('Reporte de Movimientos');
        $pdf->SetHeaderData('', 0, 'Reporte de Movimientos', '');

        $pdf->AddPage();

        $html = '<h2>Movimientos de Inventario</h2>
        <table border="1" cellpadding="4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>';
        foreach ($movimientos as $m) {
            $html .= '<tr>
                <td>' . $m->getId() . '</td>
                <td>' . htmlspecialchars($m->getProductoNombre()) . '</td>
                <td>' . $m->getCantidad() . '</td>
                <td>' . htmlspecialchars($m->getTipo()) . '</td>
                <td>' . $m->getFecha() . '</td>
            </tr>';
        }
        $html .= '</tbody></table>';

        $pdf->writeHTML($html, true, false, true, false, '');

        // Guardar el archivo en el servidor
        $filename = 'reporte_movimientos_' . date('Ymd_His') . '.pdf';
        $filepath = __DIR__ . '/../public/reportes/' . $filename;
        $pdf->Output($filepath, 'F'); // 'F' para guardar en archivo

        return $filename;
    }
}
