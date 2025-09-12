<?php
session_start();

if (isset($_SESSION['user'])) {
    $usuario_id = $_SESSION['user']['id'];
    $nombre_usuario = $_SESSION['user']['nombre'];
    $correo_usuario = $_SESSION['user']['correo'];
    $tipo_usuario = $_SESSION['user']['tipo'];
} else {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controladores/preferenciasController.php';

// Verificar preferencias
$preferenciasController = new PreferenciasController($PDO);
$preferencias = $preferenciasController->obtenerPreferencias($usuario_id);
$mostrarModalPreferencias = empty($preferencias);

// Leer cookie del tema
$tema = (isset($_COOKIE['tema']) && $_COOKIE['tema'] === 'oscuro') ? 'tema-oscuro' : 'tema-claro';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>WATCHito - Bienvenido</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="../assets/estilos.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
</head>

<body id="body" class="<?php echo $tema; ?>">

  <header>
    <h1>¡Hola, <?php echo htmlspecialchars($nombre_usuario); ?>! bienvenido a WATCHito 🎬</h1>
    <nav class="menu-usuario">
      <button type="button" id="btn-preferencias">Preferencias</button>
      <button type="button" id="btn-cambiar-tema">Cambiar tema</button>
      <a href="perfil.php" class="btn-perfil">Mi perfil</a>
      <?php if ($tipo_usuario === 'admin') : ?>
        <a href="dasboardAdmin.php" class="btn-admin">Panel de Admin</a>
      <?php endif; ?>
      <form action="../logout.php" method="post" style="display:inline;">
        <button type="submit">Cerrar sesión</button>
      </form>
    </nav>
  </header>

  <main id="main-content">
    <!-- BANNER OSCURECIDO -->
    <section class="banner-oscuro">
      <h2>¡Disfruta la mejor selección de películas!</h2>
    </section>

    <!-- Carrusel de Recomendaciones -->
    <section class="carrusel">
      <h2>Recomendados para ti</h2>
      <div class="carrusel-contenedor" id="contenedor-recomendados"></div>
    </section>

    <!-- Secciones inferiores -->
    <section id="seccion-generos1">
      <h2>Explorar por género</h2>
      <div class="seccion-generos"></div>
    </section>

    <section>
      <h2>Últimos lanzamientos</h2>
      <div class="seccion-nuevos"></div>
    </section>

    <section>
      <h2>Populares</h2>
      <div class="seccion-populares"></div>
    </section>

    <!-- cambios cambios xdd -->

    <!-- Modal de Película -->
    <div id="modalPelicula" class="modal" style="display:none;">
      <div class="modal-content">
        <span class="close" onclick="cerrarModal()">&times;</span>
        <div class="modal-body">
          <div class="modal-left">
            <img id="modalImagen" src="" alt="Poster de la película">
          </div>
          <div class="modal-right">
            <h2 id="modalTitulo"></h2>
            <p><strong><i class="fas fa-star"></i> Calificación global:</strong> <span id="modalCalificacion"></span></p>
            <p><strong><i class="fas fa-calendar-alt"></i> Fecha de estreno:</strong> <span id="modalFecha"></span></p>
            <p><strong><i class="fas fa-book-open"></i> Sinopsis:</strong></p>
            <p id="modalSinopsis"></p>

            <p id="texto-calificacion" style="font-weight: bold; margin-bottom: 8px;">Califica esta película</p>
            <!-- Dentro del div.modal-right del modalPelicula -->
            <div id="star-rating" class="star-rating">
          </div>
          <p id="texto-tu-calificacion" style="font-weight: bold; margin-top: 8px;"></p>
            <textarea id="comentario-pelicula" placeholder="Escribe tu comentario aquí...(máximo 500 caracteres)." rows="3"></textarea>
            <button id="btn-enviar-calificacion">Enviar calificación</button>
            
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Preferencias -->
    <section id="seccion-preferencias" class="seccion-preferencias" style="display:none;">
      <form id="form-preferencias">
        <h3>Elige tus géneros favoritos</h3>
        <div id="lista-generos"></div>
        <button type="submit">Guardar preferencias</button>
      </form>
      <button type="button" id="cerrar-preferencias" style="position:absolute;top:12px;right:16px;font-size:22px;background:none;border:none;cursor:pointer;">&times;</button>
    </section>

    <div id="modal-overlay" style="display:none;"></div>
  </main>

  <!-- Datos JS desde PHP -->
  <script>
    const usuario_id = <?php echo json_encode($usuario_id); ?>;
    const mostrarModalPreferencias = <?php echo json_encode($mostrarModalPreferencias); ?>;
  </script>

  <!-- Scripts -->
  <script src="../assets/script.js"></script>
  <script src="../assets/cambiarTema.js"></script>
  <script src="../assets/preferencias.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <?php include 'footer.php'; ?>
</body>
</html>