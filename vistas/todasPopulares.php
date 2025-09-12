<?php
session_start();
require_once __DIR__ . '/../url.php';
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    header('Location: ' . URL_LOGIN);
    exit();
}
$usuario_id = $_SESSION['user']['id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Películas Populares</title>
  <link rel="stylesheet" href="../assets/estilos.css">
</head>
<body>
<?php include 'header.php'; ?>
<main>
  <section>
    <h2>Películas Populares</h2>
    <div class="seccion-populares" id="todas-populares"></div>
  </section>
</main>
<?php include 'footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  fetch('../Api/apiRest.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=populares'
  })
  .then(res => res.json())
  .then(data => {
    const contenedor = document.getElementById('todas-populares');
    contenedor.innerHTML = '';
    if (!data.peliculas || !data.peliculas.length) {
      contenedor.innerHTML = '<p style="color:#888">No hay películas populares disponibles.</p>';
      return;
    }
    data.peliculas.forEach(peli => {
      const card = document.createElement('div');
      card.className = 'card';
      card.innerHTML = `
        <img src="${peli.imagen || '../assets/img/noimg.png'}" alt="${peli.titulo || 'Sin título'}">
        <h4>${peli.titulo || 'Sin título'}</h4>
        <p>${peli.sinopsis ? peli.sinopsis.substring(0, 90) + '...' : 'Sinopsis no disponible.'}</p>
      `;
      contenedor.appendChild(card);
    });
  });
});
</script>
</body>
</html>
