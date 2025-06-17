<?php
require_once __DIR__ . '/../../model/Usuario.php';

$success = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $rol_id = $_POST['rol_id'] ?? 2;
    $estado = $_POST['estado'] ?? 1;



    if (empty($nombre) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = "Todos los campos son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El correo electrónico no es válido.";
    } elseif ($password !== $confirmPassword) {
        $error = "Las contraseñas no coinciden.";
    } elseif (Usuario::buscarPorEmail($email)) {
        $error = "Ya existe un usuario registrado con ese correo.";
    } else {

        $usuario = Usuario::crear($nombre, $email, $password, $rol_id, $estado);

        if (!$usuario) {
            error_log("Error al crear usuario: nombre=$nombre, email=$email, rol_id=$rol_id, estado=$estado");
        }
        if ($usuario) {

            header("Location: register.php?success=1");
            exit;
        } else {
            $error = "Error al registrar el usuario. Intenta de nuevo.";
        }
    }
}

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success = "¡Usuario registrado correctamente!";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario | Sistema de Inventarios</title>
    <!-- Favicon -->
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link href="assets/css/register.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header bg-gradient-primary text-white">
                        <h3 class="text-center my-3">Registro de Usuario</h3>
                    </div>
                    <div class="card-body p-5">
                        <!-- Mensajes de retroalimentación -->
                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($success) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php elseif ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="nombre" class="form-label">Nombre completo</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                                            <div class="invalid-feedback">
                                                Por favor ingrese su nombre
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="email" class="form-label">Correo electrónico</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                            <div class="invalid-feedback">
                                                Por favor ingrese un correo válido
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="password" class="form-label">Contraseña</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                            <span class="input-group-text password-toggle" style="cursor: pointer;">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                            <div class="invalid-feedback">
                                                La contraseña debe tener al menos 8 caracteres
                                            </div>
                                        </div>
                                        <div class="password-strength mt-2">
                                            <div class="password-strength-bar"></div>
                                        </div>
                                        <small class="text-muted">Mínimo 8 caracteres</small>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="confirm_password" class="form-label">Confirmar contraseña</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                            <span class="input-group-text password-toggle" style="cursor: pointer;">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                            <div class="invalid-feedback">
                                                Las contraseñas no coinciden
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="rol_id" class="form-label">Rol del usuario</label>
                                        <select class="form-select" id="rol_id" name="rol_id" required>
                                            <option value="" disabled>Seleccione un rol</option>
                                            <option value="1">Administrador</option>
                                            <option value="2" selected>Usuario</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Por favor seleccione un rol
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="estado" class="form-label">Estado</label>
                                        <select class="form-select" id="estado" name="estado" required>
                                            <option value="1" selected>Activo</option>
                                            <option value="0">Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="usuarios.php" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-arrow-left me-2"></i>Regresar
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-user-plus me-2"></i>Registrar Usuario
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center py-3 bg-light">
                        <small class="text-muted">© 2025 StockManager. Todos los derechos reservados.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Scripts personalizados -->
    <script src="assets/js/register.js"></script>
</body>

</html>