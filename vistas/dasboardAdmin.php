<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['tipo'] !== 'admin') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de Administración</title>
  <link rel="stylesheet" href="../estilos/admin.css">
  <link rel="icon" href="../assets/favicon.png" type="image/png">
</head>
<body>
 <header class="header">

  <div>
  <h1 class="logo">🎬 WATCHito</h1>
  <nav class="menu-navegacion">
    <?php require_once __DIR__ . '/../url.php'; ?>
    
      <ul class="menu-lista">
      <li><a href="<?php echo URL_HOME; ?>">Inicio</a></li>
      <?php if ($_SESSION['user']['tipo'] === 'admin') : ?>
        <li><a href="<?php echo URL_DASHBOARD; ?>">Admin</a></li>
      <?php endif; ?>
      <li>
        <form action="<?php echo URL_LOGOUT; ?>" method="post">
          <button type="submit" class="btn-logout">Cerrar sesión</button>
        </form>
      </li>
    </ul>
  </nav>
  </div>
</header>

  <main>
    <input type="text" id="buscador" placeholder="Buscar películas...">
    <h2>Administrar contenido </h2>

    <!-- Contenedor de tarjetas -->
    <div id="lista-contenido" class="card-container">
      <!-- JS inserta aquí las cards -->
    </div>

    <!-- Modal de Edición -->
    <div id="modal-editar" class="modal hidden">
      <form id="form-editar">
        <input type="text" id="edit-titulo" placeholder="Título">
        <textarea id="edit-sinopsis" placeholder="Sinopsis"></textarea>
        <input type="text" id="edit-imagen" placeholder="URL imagen">
        <input type="hidden" id="edit-id">
        <input type="hidden" id="edit-tipo">
        <div class="botones-modal">
          <button type="submit">Guardar cambios</button>
          <button type="button" onclick="cerrarEdicion()">Cancelar</button>
        </div>
      </form>
    </div>
  </main>

  <?php include 'footer.php'; ?>

  <script src="../assets/admin.js"></script>
</body>
</html>
