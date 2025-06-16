<?php
session_start();
require_once __DIR__ . '../../../controller/authController.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $recordarme = isset($_POST['recordarme']);

    $resultado = Autenticacion::login($email, $password, $recordarme);

    if ($resultado === true) {
        header("Location: dashboard.php");
        exit();
    } else {
        $mensaje = $resultado;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Inventarios</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link href="assets/css/login.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow login-card">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0"><i class="fas fa-boxes me-2"></i>Inventarios</h4>
                        <small>Iniciar sesión</small>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($mensaje)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($mensaje) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form id="loginForm" method="POST" novalidate>
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="inputEmail" name="email" placeholder="correo@ejemplo.com" required autofocus>
                                <label for="inputEmail"><i class="fas fa-envelope me-2"></i>Correo Electrónico</label>
                            </div>

                            <div class="form-floating mb-3 position-relative">
                                <input type="password" class="form-control" id="inputPassword" name="password" placeholder="Contraseña" required>
                                <label for="inputPassword"><i class="fas fa-lock me-2"></i>Contraseña</label>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="inputRememberPassword" name="recordarme">
                                <label class="form-check-label" for="inputRememberPassword">Recordarme</label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    Ingresar
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center text-muted small">
                        &copy; <?= date('Y') ?> Sistema de Inventarios
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="assets/js/login.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
