<?php
require_once __DIR__ . '/../../config/Conection.php';
require_once __DIR__ . '/../Movimiento.php';

class MovimientoRepository
{
    private $db;

    public function __construct()
    {
        $this->db = conectarBD();
    }

    /**
     * Devuelve todos los movimientos de la base de datos
     * @return Movimiento[]
     */
    public function listarTodos(): array
    {
        $stmt = $this->db->query("
            SELECT m.id, m.producto_id, m.cantidad, m.tipo, m.fecha, m.estado, m.created_at, m.updated_at, p.nombre AS producto_nombre
            FROM movimientos m
            JOIN productos p ON m.producto_id = p.id
            ORDER BY m.fecha DESC
        ");
        $movimientos = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $movimiento = new Movimiento(
                $fila['id'],
                $fila['producto_id'],
                $fila['cantidad'],
                $fila['tipo'],
                $fila['fecha'],
                $fila['estado'],
                $fila['created_at'],
                $fila['updated_at'] ?? null
            );
            // Asignar el nombre del producto como propiedad dinámica
            $movimiento->producto_nombre = $fila['producto_nombre'];
            $movimientos[] = $movimiento;
        }
        return $movimientos;
    }
    /**
     * Obtiene las ventas de los últimos N días
     * @param int $dias Número de días a consultar
     * @return array Array asociativo con la fecha como clave y el total de ventas como valor
     */
    public function obtenerVentasUltimosDias($dias = 7)
    {
        $stmt = $this->db->prepare("
        SELECT DATE(fecha) as dia, COUNT(*) as total
        FROM movimientos
        WHERE tipo = 'venta' AND fecha >= CURRENT_DATE - INTERVAL ':dias days'
        GROUP BY dia
        ORDER BY dia ASC
    ");
        $stmt->execute(['dias' => $dias]);
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[$row['dia']] = (int)$row['total'];
        }
        return $result;
    }
}
