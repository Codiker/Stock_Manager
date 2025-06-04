<?php

include __DIR__ . '/../config/Conection.php';

class UserController
{
    public function getUserById($id)
    {
        // Conexión
        $conn = conectarBD();
        $stmt = $conn->prepare("SELECT id, nombre, correo FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Agrega aquí más funciones como crear, editar, eliminar, listar
}