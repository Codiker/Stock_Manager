<?php
require_once __DIR__ . '/../../config/Conection.php';
require_once  __DIR__ . '/../Product.php';

class ProductRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = conectarBD();
    }

    public function guardar(Producto $producto): bool
    {
        try {
            if ($producto->getId()) {
                $query = "UPDATE productos SET
                            nombre = :nombre,
                            descripcion = :descripcion,
                            precio = :precio,
                            stock = :stock,
                            categoria_id = :categoria_id,
                            activo = :activo,
                            estado = :estado,
                            updated_at = CURRENT_TIMESTAMP
                          WHERE id = :id";
            } else {
                $query = "INSERT INTO productos 
                         (nombre, descripcion, precio, stock, categoria_id, activo, estado, created_at)
                         VALUES (:nombre, :descripcion, :precio, :stock, :categoria_id, :activo, :estado, :created_at)";
            }

            $stmt = $this->db->prepare($query);

            $params = [
                ':nombre' => $producto->getNombre(),
                ':descripcion' => $producto->getDescripcion(),
                ':precio' => $producto->getPrecio(),
                ':stock' => $producto->getStock(),
                ':categoria_id' => $producto->getCategoriaId(),
                ':activo' => $producto->isActivo(),
                ':estado' => $producto->getEstado()
            ];

            if ($producto->getId()) {
                $params[':id'] = $producto->getId();
            } else {
                $params[':created_at'] = $producto->getCreatedAt();
            }

            $result = $stmt->execute($params);

            if (!$producto->getId() && $result) {
                $productoId = (int)$this->db->lastInsertId();
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Error al guardar producto: " . $e->getMessage());
            return false;
        }
    }

    public function buscarPorId(int $id): ?Producto
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM productos WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);

            return $datos ? new Producto(
                (int)$datos['id'],
                $datos['nombre'],
                $datos['descripcion'],
                (float)$datos['precio'],
                (int)$datos['stock'],
                (int)$datos['categoria_id'],
                (bool)$datos['activo'],
                $datos['estado'],
                $datos['created_at'],
                $datos['updated_at'] ?? null
            ) : null;
        } catch (PDOException $e) {
            error_log("Error al buscar producto: " . $e->getMessage());
            return null;
        }
    }

    public function desactivarProducto(int $id): bool
    {
        error_log("Desactivando producto con ID: $id");
        try {
            $stmt = $this->db->prepare("UPDATE productos SET activo =false WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Error al eliminar producto: " . $e->getMessage());
            return false;
        }
    }

    public function listarTodos(int $limite = 100, int $offset = 0): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, c.nombre AS categoria_nombre
                FROM productos p
                JOIN categorias c ON p.categoria_id = c.id
                ORDER BY p.created_at DESC
                LIMIT :limite OFFSET :offset
            ");
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $productos = [];
            while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $producto = new Producto(
                    (int)$datos['id'],
                    $datos['nombre'],
                    $datos['descripcion'],
                    (float)$datos['precio'],
                    (int)$datos['stock'],
                    (int)$datos['categoria_id'],
                    (bool)$datos['activo'],
                    $datos['estado'],
                    $datos['created_at'],
                    $datos['updated_at'] ?? null
                );
                $producto->categoria_nombre = $datos['categoria_nombre'];
                $productos[] = $producto;
            }

            return $productos;
        } catch (PDOException $e) {
            error_log("Error al listar productos: " . $e->getMessage());
            return [];
        }
    }

    public function filtrar() {}

    public function listarActivos(int $limite = 100, int $offset = 0): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, c.nombre AS categoria_nombre
                FROM productos p
                JOIN categorias c ON p.categoria_id = c.id
                WHERE p.activo = true
                ORDER BY p.id
                LIMIT :limite OFFSET :offset
            ");
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $this->mapearProductos($stmt);
        } catch (PDOException $e) {
            error_log("Error al listar productos activos: " . $e->getMessage());
            return [];
        }
    }

    public function contarTotalProductos(): int
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM productos WHERE activo = true");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error al contar productos: " . $e->getMessage());
            return 0;
        }
    }

    public function contarProductosAgotados(): int
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM productos WHERE stock = 0 AND activo = true");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error al contar productos agotados: " . $e->getMessage());
            return 0;
        }
    }

    public function contarProductosBajoStock(): int
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM productos WHERE stock <= 10 AND activo = true");
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error al contar productos bajo stock: " . $e->getMessage());
            return 0;
        }
    }

    public function calcularValorTotalInventario(): float
    {
        try {
            $stmt = $this->db->query("SELECT SUM(precio * stock) FROM productos WHERE activo = true");
            return (float) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error al calcular valor total: " . $e->getMessage());
            return 0.0;
        }
    }

    public function obtenerProductosAgotados(): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, c.nombre AS categoria_nombre
                FROM productos p
                JOIN categorias c ON p.categoria_id = c.id
                WHERE p.stock = 0 AND p.activo = true
            ");
            $stmt->execute();
            return $this->mapearProductos($stmt);
        } catch (PDOException $e) {
            error_log("Error al obtener productos agotados: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerProductosBajoStock(): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, c.nombre AS categoria_nombre
                FROM productos p
                JOIN categorias c ON p.categoria_id = c.id
                WHERE p.stock <= 10 AND p.stock > 0 AND p.activo = true
                ORDER BY p.stock ASC
            ");
            $stmt->execute();
            return $this->mapearProductos($stmt);
        } catch (PDOException $e) {
            error_log("Error al obtener productos bajo stock: " . $e->getMessage());
            return [];
        }
    }

    private function mapearProductos($stmt): array
    {
        $productos = [];
        while ($datos = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $producto = new Producto(
                (int)$datos['id'],
                $datos['nombre'],
                $datos['descripcion'],
                (float)$datos['precio'],
                (int)$datos['stock'],
                (int)$datos['categoria_id'],
                (bool)$datos['activo'],
                $datos['estado'],
                $datos['created_at'],
                $datos['updated_at'] ?? null
            );
            $producto->categoria_nombre = $datos['categoria_nombre'];
            $productos[] = $producto;
        }
        return $productos;
    }

    public function contarProductosPorCategoria()
    {
        $stmt = $this->db->query("
        SELECT c.nombre as categoria, COUNT(p.id) as total
        FROM productos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        GROUP BY c.nombre
    ");
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[$row['categoria'] ?? 'Sin categoría'] = (int)$row['total'];
        }
        return $result;
    }
}
