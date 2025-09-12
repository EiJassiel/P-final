<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['user']['id'];
$nombre_usuario = $_SESSION['user']['nombre'];
$correo_usuario = $_SESSION['user']['correo'];
$tipo_usuario = $_SESSION['user']['tipo'];

require_once __DIR__ . '/../config/db.php';

// Leer cookie del tema
$tema = (isset($_COOKIE['tema']) && $_COOKIE['tema'] === 'oscuro') ? 'tema-oscuro' : 'tema-claro';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Perfil de <?php echo htmlspecialchars($nombre_usuario); ?> - WATCHito</title>
  <link rel="stylesheet" href="../assets/estilos.css">
  <script>
    const usuario_id = <?php echo json_encode($usuario_id); ?>;
  </script>
</head>

<body class="<?php echo $tema; ?>">
  <header>
    <h1>Perfil de <?php echo htmlspecialchars($nombre_usuario); ?></h1>
    <nav class="menu-usuario">
      <a href="home.php">Inicio</a>
      <form action="../logout.php" method="post" style="display:inline;">
        <button type="submit">Cerrar sesión</button>
      </form>
    </nav>
  </header>

<main>
  <!-- Info usuario -->
  <section class="perfil-info">
    <br>
    <br>
    <h2>Información del usuario</h2>
    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($nombre_usuario); ?></p>
    <p><strong>Correo:</strong> <?php echo htmlspecialchars($correo_usuario); ?></p>
  </section>

  <!-- Sección películas calificadas -->
  <section class="perfil-seccion" id="seccion-calificadas">
    <h2>Películas que has calificado</h2>
    <div class="carrusel-perfil" id="pelis-calificadas"></div>
  </section>
</main>
  <script src="../assets/perfil.js"></script>
  <?php include 'footer.php'; ?>
</body>
</html>
