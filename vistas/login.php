<?php
session_start();
require_once __DIR__ . '/../url.php';
if (isset($_SESSION['user'])) {
    header('Location: ' . URL_HOME);
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="../assets/estilos.css">
  <link rel="stylesheet" href="../assets/auth.css">
</head>
<body class="auth-bg">
    <div class="auth-container login">
        <h2>Iniciar Sesión</h2>
        <form id="formLogin">
            <div class="form-group">
                <label for="email">Correo</label>
                <input type="email" id="email" name="correo" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div id="login-error" class="form-error"></div>
            <button type="submit">Entrar</button>
        </form>
        <p>¿No tienes una cuenta? <a href="../vistasValidacion/register.html">Regístrate aquí</a></p>
    </div>
    <script src="../assets/lloginscript.js"></script>
</body>
</html>