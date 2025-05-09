<?php
require_once 'model/Usuario.php'; // Asegúrate de que la ruta sea correcta

// Datos del nuevo usuario
$nombre = "Juan Pérez";
$email = "juan@example.com";
$password = "123456"; // Contraseña sin encriptar
$rol = "2"; // Ajusta según tus roles
$estado = "1"; // Ajusta si usas 'activo', 'inactivo', etc.

// Encriptar contraseña
$passwordEncriptada = password_hash($password, PASSWORD_BCRYPT);

$usuario = new Usuario($nombre, $email, $passwordEncriptada, $rol, $estado);
//             $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = :email");
//             $stmt->bindParam(':email', $email);
//             $stmt->execute();
//             $usuarioExistente = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario->crear($nombre, $email, $passwordEncriptada, $rol, $estado)) {
    echo "✅ Usuario creado correctamente.\n";
    echo "Correo: $email\n";
    echo "Contraseña: $password\n";
} else {
    echo "❌ Error al crear el usuario.\n";
}
?>
