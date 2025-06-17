<?php
require_once __DIR__ . '/../config/Conection.php';

class Usuario
{
    private $id;
    private $nombre;
    private $email;
    private $password;
    private $rol_id;
    private $estado;

    public function __construct($nombre, $email, $password, $rol_id, $estado, $id = null)
    {
       
        $this->nombre = $nombre;
        $this->email = $email;
        $this->password = $password;
        $this->rol_id = $rol_id;
        $this->estado = $estado;
        $this->id = $id;
    }

    // Getters
    public function getId()       { return $this->id; }
    public function getNombre()   { return $this->nombre; }
    public function getEmail()    { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getRolId()    { return $this->rol_id; }
    public function getEstado()   { return $this->estado; }

    // Método para buscar usuario por email (requerido en authController)
    public static function buscarPorEmail(string $email): ?Usuario
    {
        try {
            $db = conectarBD();
            $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$datos) return null;

            return new Usuario(
                $datos['nombre'],
                $datos['email'],
                $datos['password'],
                $datos['rol_id'],
                $datos['estado'],
                $datos['id']
            );
        } catch (PDOException $e) {
            error_log("Error en buscarPorEmail: " . $e->getMessage());
            return null;
        }
    }

    // Método de creación de usuario mejorado
    public static function crear($nombre, $email, $password, $rol_id, $estado): ?Usuario
    {
        // Validación básica
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Email inválido");
        }

        try {
            $db = conectarBD();
            $hashedClave = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO usuarios (nombre, email, password, rol_id, estado)
                      VALUES (:nombre, :email, :password, :rol_id, :estado)
                      RETURNING id";  // PostgreSQL requiere RETURNING para lastInsertId

            $stmt = $db->prepare($query);
            $stmt->execute([
                ':nombre' => $nombre,
                ':email' => $email,
                ':password' => $hashedClave,
                ':rol_id' => (int)$rol_id,
                ':estado' => (int)$estado
            ]);

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $nuevoId = $resultado['id'] ?? null;

            return $nuevoId ? new Usuario($nombre, $email, $hashedClave, $rol_id, $estado, $nuevoId) : null;
        } catch (PDOException $e) {
            error_log("Error al crear usuario: " . $e->getMessage());
            return null;
        }
    }

    // Método adicional recomendado: Actualizar estado
    public static function actualizarEstado(int $id, int $estado): bool
    {
        try {
            $db = conectarBD();
            $stmt = $db->prepare("UPDATE usuarios SET estado = :estado WHERE id = :id");
            return $stmt->execute([':id' => $id, ':estado' => $estado]);
        } catch (PDOException $e) {
            error_log("Error al actualizar estado: " . $e->getMessage());
            return false;
        }
    }
}