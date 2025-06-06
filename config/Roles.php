<?php
function esAdmin() {
    return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 1;
}
function esUsuario() {
    return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 2;
}