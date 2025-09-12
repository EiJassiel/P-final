document.addEventListener('DOMContentLoaded', function () {
  // Navegación a Populares
  document.querySelectorAll('.seccion-populares').forEach(seccion => {
    seccion.addEventListener('click', function (e) {
      const rect = seccion.getBoundingClientRect();
      if (e.clientX > rect.right - 60) {
        window.location.href = '../vistas/todasPopulares.php';
      }
    });
  });

  // Renderizar géneros desde archivo local
  fetch('../models/generos.json')
    .then(res => res.json())
    .then(data => {
      if (Array.isArray(data)) {
        renderGeneros(data);
      } else if (Array.isArray(data.generos)) {
        renderGeneros(data.generos);
      }
    });

  // Mostrar recomendaciones personalizadas
  if (typeof usuario_id !== 'undefined' && usuario_id) {
    fetch('../Api/apiRest.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `action=obtener_recomendados&usuario_id=${encodeURIComponent(usuario_id)}`
    })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'ok' && Array.isArray(data.peliculas)) {
          renderCarrusel(data.peliculas);
        } else {
          renderCarrusel([]);
        }
      });
  }

  // Cargar populares
  fetch('../Api/apiRest.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=populares'
  })
    .then(res => res.json())
    .then(data => {
      renderSeccion(data.peliculas || [], 'seccion-populares');
    });

  // Nuevos lanzamientos
  fetch('../Api/apiRest.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=nuevos'
  })
    .then(res => res.json())
    .then(data => {
      renderSeccion(data.peliculas || [], 'seccion-nuevos');
    });

  // Función para mostrar el modal de preferencias
  function mostrarPreferencias() {
    const modal = document.getElementById('modal-preferencias');
    if (modal) modal.classList.remove('hidden');
  }

  function renderGeneros(generos) {
    const contenedor = document.querySelector('.seccion-generos');
    if (!contenedor) return;

    contenedor.innerHTML = '';

    if (!Array.isArray(generos) || generos.length === 0) {
      contenedor.innerHTML = '<p style="color:#888">No hay géneros disponibles.</p>';
      return;
    }

    generos.forEach(genero => {
      const card = document.createElement('div');
      card.classList.add('card-genero-tmdb');

      const imagen = document.createElement('img');
      imagen.classList.add('img-genero-tmdb');
      imagen.src = genero.imagen || '/ProyectoSemestral/assets/img/noimg.png';
      imagen.alt = genero.nombre || genero.name || 'Sin nombre';
      imagen.onerror = () => {
        imagen.src = '/ProyectoSemestral/assets/img/noimg.png';
      };

      const overlay = document.createElement('div');
      overlay.classList.add('nombre-genero-overlay');
      overlay.textContent = genero.nombre || genero.name || 'Sin nombre';

      card.appendChild(imagen);
      card.appendChild(overlay);

      card.addEventListener('click', () => {
        mostrarPeliculasPorGenero(genero.id);
      });

      contenedor.appendChild(card);
    });
  }

  function mostrarPeliculasPorGenero(idGenero) {
    fetch('../Api/apiRest.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `action=explorar_genero&id_genero=${encodeURIComponent(idGenero)}`
    })
      .then(res => res.json())
      .then(data => {
        if (!data.peliculas) return;

        // Filtrar las películas por género
        const peliculasFiltradas = data.peliculas.filter(pelicula => {
          // Revisar si el idGenero está presente en el array genre_ids
          return pelicula.genre_ids.includes(idGenero);
        });

        // Llamamos a la función para renderizar las películas filtradas
        renderSeccion(peliculasFiltradas, 'seccion-generos');
      })
      .catch(error => {
        console.error('Error al obtener las películas:', error);
      });
  }

  function renderCarrusel(peliculas) {
    const contenedor = document.getElementById('contenedor-recomendados');
    contenedor.innerHTML = '';
    if (!peliculas.length) {
      contenedor.innerHTML = '<p style="color:#888">No hay recomendaciones por ahora.</p>';
      return;
    }
    peliculas.slice(0, 4).forEach(peli => {
      const imagen = peli.poster_path
        ? `https://image.tmdb.org/t/p/w342${peli.poster_path}`
        : '../assets/img/noimg.png';
      const titulo = peli.title || 'Sin título';
      const sinopsis = peli.overview || 'Sinopsis no disponible.';

      const card = document.createElement('div');
      card.className = 'card-horizontal';
      card.innerHTML = `
        <img class="poster-carrusel" src="${imagen}" alt="${titulo}">
        <div class="info-carrusel">
          <h4>${titulo}</h4>
          <p>${sinopsis.substring(0, 120)}...</p>
        </div>
      `;
      contenedor.appendChild(card);
    });
  }

  function renderSeccion(peliculas, seccionId) {
    const contenedor = document.querySelector('.' + seccionId);
    if (!contenedor) return;

    contenedor.innerHTML = '';

    if (!peliculas.length) {
      contenedor.innerHTML = '<p style="color:#888">No hay contenido disponible.</p>';
      return;
    }

    peliculas.slice(0, 20).forEach(peli => {
      const titulo = peli.titulo || peli.title || 'Sin título';
      const imagen = peli.imagen || (peli.poster_path ? `https://image.tmdb.org/t/p/w500${peli.poster_path}` : '../assets/img/noimg.png');
      const fecha = peli.fecha_lanzamiento || peli.release_date || 'Fecha no disponible';
      const calificacion = peli.calificacion !== undefined ? peli.calificacion : (peli.vote_average !== undefined ? peli.vote_average : 'N/A');
      const sinopsis = peli.sinopsis || peli.overview || 'Sinopsis no disponible.';

      const datosPeli = {
        id_tmdb: peli.id ?? peli.id_tmdb ?? '', 
        titulo,
        imagen,
        fecha_lanzamiento: fecha,
        calificacion,
        sinopsis
      };

      const card = document.createElement('div');
      card.className = 'card';
      card.dataset.pelicula = JSON.stringify(datosPeli);

      card.innerHTML = `
        <img src="${imagen}" alt="${titulo}">
        <div class="card-content">
          <h4>${titulo}</h4>
          <p>${fecha}</p>
          <p><i class="fas fa-star"></i> ${calificacion}</p>
        </div>
      `;

      card.addEventListener('click', () => {
        abrirModal(datosPeli);
      });

      contenedor.appendChild(card);
    });
  }

  function abrirModal(pelicula) {
    const modal = document.getElementById('modalPelicula');
    const modalImagen = document.getElementById('modalImagen');
    const modalTitulo = document.getElementById('modalTitulo');
    const modalCalificacion = document.getElementById('modalCalificacion');
    const modalFecha = document.getElementById('modalFecha');
    const modalSinopsis = document.getElementById('modalSinopsis');
    const textoTuCalificacion = document.getElementById('texto-tu-calificacion');

    modalImagen.src = pelicula.imagen || '../assets/img/noimg.png';
    modalImagen.alt = pelicula.titulo || 'Poster de la película';

    // Guardar id_tmdb en atributo data para usarlo después
    modalImagen.setAttribute('data-tmdb', pelicula.id_tmdb || '');

    modalTitulo.textContent = pelicula.titulo || 'Sin título';
    modalCalificacion.textContent = pelicula.calificacion !== undefined ? pelicula.calificacion : 'N/A';
    modalFecha.textContent = pelicula.fecha_lanzamiento || 'Fecha no disponible';
    modalSinopsis.textContent = pelicula.sinopsis || 'Sinopsis no disponible.';

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Cargar calificación previa para pintar estrellas y mostrar texto
    cargarCalificacion(pelicula.id_tmdb || '');
  }

  function cerrarModal() {
    const modal = document.getElementById('modalPelicula');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
  }

  // ===== MANEJO DE CALIFICACIÓN POR ESTRELLAS =====
  let currentRating = 0;
  let yaVotado = false;

  function pintarEstrellas(rating, bloqueadas = false) {
    const starContainer = document.getElementById('star-rating');
    if (!starContainer) {
      console.error('No se encontró el contenedor #star-rating');
      return;
    }
    starContainer.innerHTML = '';
    for (let i = 1; i <= 5; i++) {
      const star = document.createElement('span');
      star.className = 'star';
      star.dataset.value = i;
      star.innerHTML = i <= rating ? '★' : '☆';
      star.style.cursor = bloqueadas ? 'not-allowed' : 'pointer';
      star.style.color = i <= rating ? '#FFD700' : '#ccc';
      if (!bloqueadas) {
        star.onclick = function () {
          currentRating = i;
          pintarEstrellas(i, false);
        };
      }
      starContainer.appendChild(star);
    }

    const textoCalificacion = document.getElementById('texto-tu-calificacion');
    if (textoCalificacion) {
      if (rating > 0) {
        textoCalificacion.textContent = bloqueadas
          ? '¡Gracias por votar!'
          : `Tu calificación: ${'★'.repeat(rating)}${'☆'.repeat(5 - rating)}`;
      } else {
        textoCalificacion.textContent = '';
      }
    }

    const btnEnviar = document.getElementById('btn-enviar-calificacion');
    if (btnEnviar) btnEnviar.disabled = bloqueadas;
  }

  function guardarCalificacion() {
    const id_tmdb = document.getElementById('modalImagen').getAttribute('data-tmdb');
    const comentario = document.getElementById('comentario-pelicula').value;

    if (!id_tmdb || !usuario_id) {
      alert('Error: usuario no identificado o película inválida.');
      return;
    }
    if (currentRating === 0) {
      alert('Debes seleccionar una calificación antes de enviar.');
      return;
    }

    const bodyData = `action=guardar_calificacion` +
      `&usuario_id=${encodeURIComponent(usuario_id)}` +
      `&id_tmdb=${encodeURIComponent(id_tmdb)}` +
      `&tipo=pelicula` +
      `&puntuacion=${encodeURIComponent(currentRating)}` +
      `&comentario=${encodeURIComponent(comentario)}`;

    fetch('../Api/apiRest.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: bodyData
    })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'ok') {
          yaVotado = true;
          pintarEstrellas(currentRating, true);
          alert('¡Gracias por calificar!');
        } else {
          alert('No se pudo guardar tu calificación. Intenta de nuevo.');
        }
      })
      .catch(() => {
        alert('Error al enviar tu calificación.');
      });
  }

  function cargarCalificacion(id_tmdb) {
    if (!id_tmdb || !usuario_id) {
      pintarEstrellas(0, false);
      return;
    }

    const bodyData = `action=obtener_calificacion` +
      `&usuario_id=${encodeURIComponent(usuario_id)}` +
      `&id_tmdb=${encodeURIComponent(id_tmdb)}` +
      `&tipo=pelicula`;

    fetch('../Api/apiRest.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: bodyData
    })
      .then(response => response.json())
      .then(data => {
        const puntuacion = data.calificacion ? parseInt(data.calificacion) : 0;
        currentRating = puntuacion;
        yaVotado = puntuacion > 0;
        pintarEstrellas(puntuacion, yaVotado);
      })
      .catch(() => {
        pintarEstrellas(0, false);
      });
  }

  // Eventos botones
  const btnEnviar = document.getElementById('btn-enviar-calificacion');
  if (btnEnviar) {
    btnEnviar.addEventListener('click', () => {
      if (!yaVotado) guardarCalificacion();
    });
  }

  const closeBtn = document.querySelector('.close');
  if (closeBtn) {
    closeBtn.addEventListener('click', cerrarModal);
  }

  document.querySelector('.close').addEventListener('click', function () {
    cerrarModal();
  });
});
