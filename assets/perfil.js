document.addEventListener('DOMContentLoaded', function () {
  const contenedor = document.getElementById('pelis-calificadas');
  if (!contenedor || typeof usuario_id === 'undefined') return;

  fetch('../Api/apiRest.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `action=peliculas_calificadas&usuario_id=${encodeURIComponent(usuario_id)}`
  })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'ok') {
        renderPeliculas(data.peliculas, contenedor);
      } else {
        contenedor.textContent = data.msg;
      }
    })
    .catch(err => {
      contenedor.textContent = 'Error al cargar películas';
      console.error(err);
    });

  function renderPeliculas(peliculas, contenedor) {
    if (!peliculas.length) {
      contenedor.textContent = 'No has calificado ninguna película aún.';
      return;
    }

    contenedor.innerHTML = ''; // limpiar

    peliculas.forEach(peli => {
      const card = document.createElement('div');
      card.className = 'card';
      card.title = peli.titulo;

      const img = document.createElement('img');
      img.src = peli.imagen;
      img.alt = peli.titulo;

      const titulo = document.createElement('h4');
      titulo.textContent = peli.titulo;

      const calif = document.createElement('p');
      calif.textContent = `Calificación: ${peli.puntuacion} ★`;

      const btn = document.createElement('button');
      btn.className = 'btn-eliminar';
      btn.dataset.id = peli.id_tmdb;
      btn.style.cssText = 'background:none; border:none; padding:0; cursor:pointer;';
      btn.innerHTML = `<img src="../assets/img/delete.png" alt="Eliminar Calificación" style="width:20px; height:20px;" />`;

      card.appendChild(img);
      card.appendChild(titulo);

      if (peli.comentario) {
        const comentario = document.createElement('p');
        comentario.textContent = `"${peli.comentario}"`; // Escapado para evitar XSS
        card.appendChild(comentario);
      }

      card.appendChild(calif);
      card.appendChild(btn);
      contenedor.appendChild(card);
    });

    contenedor.querySelectorAll('.btn-eliminar').forEach(btn => {
      btn.addEventListener('click', () => {
        const idTmdb = btn.dataset.id;
        if (!confirm('¿Estás seguro de que quieres quitar tu calificación?')) return;

        fetch('../Api/apiRest.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `action=eliminar_calificacion&usuario_id=${encodeURIComponent(usuario_id)}&id_tmdb=${encodeURIComponent(idTmdb)}`
        })
          .then(res => res.text())
          .then(text => {
            console.log('Respuesta cruda:', text);
            return JSON.parse(text);
          })
          .then(data => {
            if (data.status === 'ok') {
              btn.closest('.card').remove();
            } else {
              alert('No se pudo eliminar la calificación: ' + data.msg);
            }
          })
          .catch(err => {
            console.error('Error al eliminar:', err);
          });
      });
    });
  }
});
