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
    { error_log("Desactivando producto con ID: $id");
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

    public function filtrar (){
        
    }

    public function listarActivos(int $limite = 100, int $offset = 0): array
{
    try {
        $stmt = $this->db->prepare("
            SELECT p.*, c.nombre AS categoria_nombre
            FROM productos p
            JOIN categorias c ON p.categoria_id = c.id
            WHERE p.activo = true
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
        error_log("Error al listar productos activos: " . $e->getMessage());
        return [];
    }
}
}