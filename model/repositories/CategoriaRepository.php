<?php
require_once __DIR__ . '/../../config/Conection.php';
require_once __DIR__ . '/../Categoria.php';

class CategoriaRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = conectarBD();
    }

    public function listarTodas()
    {
        $stmt = $this->pdo->query("SELECT id, nombre FROM categorias");
        $categorias = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categorias[] = $fila;
        }
        return $categorias;
    }

    public function contarCategorias(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM categorias");
        return (int) $stmt->fetchColumn();
    }

    public function agregar($nombre, $descripcion)
    {
        $stmt = $this->pdo->prepare("INSERT INTO categorias (nombre, descripcion, created_at) VALUES (?, ?, NOW())");
        return $stmt->execute([$nombre, $descripcion]);
    }

    public function actualizar($id, $nombre, $descripcion)
    {
        $stmt = $this->pdo->prepare("UPDATE categorias SET nombre = ?, descripcion = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$nombre, $descripcion, $id]);
    }

    public function activar($id)
    {
        $stmt = $this->pdo->prepare("UPDATE categorias SET estado = true WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function desactivar($id)
    {
        $stmt = $this->pdo->prepare("UPDATE categorias SET estado = false WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function buscarPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categorias WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarActivas()
    {
        $stmt = $this->pdo->query("SELECT * FROM categorias WHERE estado = true ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarDesactivadas()
    {
        $stmt = $this->pdo->query("SELECT * FROM categorias WHERE estado = false ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
