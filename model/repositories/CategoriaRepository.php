<?php
require_once __DIR__ . '/../../config/Conection.php';
require_once __DIR__ . '/../Categoria.php';

class CategoriaRepository {
    private $pdo;

    public function __construct() {
        $this->pdo = conectarBD();
    }

    public function listarTodas() {
        $stmt = $this->pdo->query("SELECT id, nombre FROM categorias"); 
        $categorias = [];
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categorias[] = $fila;
        }
        return $categorias;
    }
}