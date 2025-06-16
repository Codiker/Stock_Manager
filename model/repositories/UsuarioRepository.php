<?php
require_once __DIR__ . '/../Usuario.php';

class UsuarioRepository
{
    public function listarTodos()
    {
        $pdo = conectarBD();
        $stmt = $pdo->query("SELECT * FROM usuarios ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id)
    {
       $pdo = conectarBD();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarDatos($id, $nombre, $rol_id, $estado)
    {
        $pdo = conectarBD();
        $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, rol_id = ?, estado = ? WHERE id = ?");
        return $stmt->execute([$nombre, $rol_id, $estado, $id]);
    }

    public function actualizarEstado($id, $estado)
    {
        $pdo = conectarBD();
        $stmt = $pdo->prepare("UPDATE usuarios SET estado = ? WHERE id = ?");
        return $stmt->execute([$estado, $id]);
    }
}