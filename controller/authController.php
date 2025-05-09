<?php
include_once __DIR__ . '/../model/Usuario.php';

class Autenticacion
{
    public static function login($email, $password, $recordarme)
    { error_log("Intentando login con: $email");
        if (empty($email) || empty($password)) {
            return "Por favor, complete todos los campos.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "El correo electrónico no es válido.";
        }

        $usuario = Usuario::buscarPorCorreo($email);
        if (!$usuario) {
            return "El correo electrónico no está registrado.";
        }
        if (!password_verify($password, $usuario->getPassword())) {
            return "La contraseña es incorrecta.";
        }
        if ($usuario->getEstado() != 1) {
            return "El usuario está inactivo, contacte al administrador.";
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['usuario_nombre'] = $usuario->getNombre();
        $_SESSION['usuario_rol']    = $usuario->getRolId();

        if ($recordarme) {
            setcookie('usuario_email', $usuario->getEmail(), time()+60*60*24*30, "/");
        }

        return true;
    }

    public static function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        setcookie('usuario_email', '', time() - 3600, "/");
    }
}
