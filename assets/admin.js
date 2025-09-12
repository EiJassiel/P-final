// admin.js - Panel de administración CineX

document.addEventListener('DOMContentLoaded', function () {
  cargarContenidoAdmin();

  const formEditar = document.getElementById('form-editar');
  if (formEditar) {
    formEditar.addEventListener('submit', function (e) {
      e.preventDefault();
      guardarCambiosEdicion();
    });
  }
});
document.addEventListener('DOMContentLoaded', function () {
  cargarContenidoAdmin();

  const formEditar = document.getElementById('form-editar');
  if (formEditar) {
    formEditar.addEventListener('submit', function (e) {
      e.preventDefault();
      guardarCambiosEdicion();
    });
  }

  const buscador = document.getElementById('buscador');
  buscador.addEventListener('input', function () {
    const filtro = this.value.toLowerCase();
    const cards = document.querySelectorAll('.card-admin');

    cards.forEach(card => {
      const titulo = card.querySelector('h4')?.textContent.toLowerCase() || '';
      card.style.display = titulo.includes(filtro) ? 'flex' : 'none';
    });
  });
});


function cargarContenidoAdmin() {
  fetch('../Api/apiRest.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=listar_cache'
  })
    .then(res => res.json())
    .then(data => {
      const contenedor = document.getElementById('lista-contenido');
      contenedor.innerHTML = '';

      if (!data.contenido || !data.contenido.length) {
        contenedor.innerHTML = '<p style="color:#888">No hay contenido cacheado.</p>';
        return;
      }

      const idsRenderizados = new Set(); // Set para evitar duplicados

      data.contenido.forEach(item => {
        if (idsRenderizados.has(item.id_tmdb)) return; // Ignorar duplicados
        idsRenderizados.add(item.id_tmdb);

        const card = document.createElement('div');
        card.className = 'card-admin';

        card.innerHTML = `
          <img src="${item.imagen || '../assets/img/noimg.png'}" alt="${item.titulo}" />
          <h4>${item.titulo || 'Sin título'}</h4>
          <p>${item.sinopsis ? item.sinopsis.substring(0, 100) + '...' : 'Sinopsis no disponible.'}</p>
          <span class="badge badge-${item.estado}">${item.estado}</span>
          <div class="acciones">
            <button onclick="editarContenido('${item.id_tmdb}','${item.tipo}')">Editar</button>
            <button onclick="cambiarEstado('${item.id_tmdb}','${item.tipo}','${item.estado === 'activo' ? 'inactivo' : 'activo'}')">
              ${item.estado === 'activo' ? 'Ocultar' : 'Mostrar'}
            </button>
          </div>
        `;

        contenedor.appendChild(card);
      });
    });
}


window.editarContenido = function (id_tmdb, tipo) {
  // Validación de id_tmdb: debe ser un número entero positivo
  if (!validarIdTmdb(id_tmdb)) {
    alert("ID de la película no válido.");
    return;
  }

  // Validación de tipo (aseguramos que tipo sea un valor válido y seguro)
  if (!validarTipo(tipo)) {
    alert("Tipo de contenido no válido.");
    return;
  }

  // Hacer el fetch al servidor
  fetch('../Api/apiRest.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `action=consultar_cache&id_tmdb=${encodeURIComponent(id_tmdb)}`
  })
  .then(res => res.json())
  .then(data => {
    if (!data.contenido) return;

    // Escapar los valores para prevenir XSS
    document.getElementById('edit-titulo').value = escapeHTML(data.contenido.titulo || '');
    document.getElementById('edit-sinopsis').value = escapeHTML(data.contenido.sinopsis || '');
    document.getElementById('edit-imagen').value = escapeHTML(data.contenido.imagen || '');
    document.getElementById('edit-id').value = escapeHTML(id_tmdb);
    document.getElementById('edit-tipo').value = escapeHTML(tipo);
    document.getElementById('modal-editar').classList.remove('hidden');
  });
};

// Validar que id_tmdb sea un número entero positivo
function validarIdTmdb(id_tmdb) {
  const regex = /^[0-9]+$/;  // Solo números enteros positivos
  return regex.test(id_tmdb);
}

// Validar que tipo sea una cadena válida (por ejemplo, 'pelicula', 'serie', etc.)
function validarTipo(tipo) {
  const tiposValidos = ['pelicula', 'serie'];
  return tiposValidos.includes(tipo);
}

// Función para escapar contenido HTML (prevención de XSS)
function escapeHTML(str) {
  const element = document.createElement('div');
  if (str) {
    element.innerText = str;
    element.textContent = str;
  }
  return element.innerHTML;
}


window.cerrarEdicion = function () {
  document.getElementById('modal-editar').classList.add('hidden');
};

function guardarCambiosEdicion() {
  const id = document.getElementById('edit-id').value;
  const tipo = document.getElementById('edit-tipo').value;
  const titulo = document.getElementById('edit-titulo').value;
  const sinopsis = document.getElementById('edit-sinopsis').value;
  const imagen = document.getElementById('edit-imagen').value;

  fetch('../Api/apiRest.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `action=guardar_cache&id_tmdb=${encodeURIComponent(id)}&tipo=${encodeURIComponent(tipo)}&titulo=${encodeURIComponent(titulo)}&sinopsis=${encodeURIComponent(sinopsis)}&imagen=${encodeURIComponent(imagen)}`
  })
    .then(res => res.json())
    .then(() => {
      cerrarEdicion();
      cargarContenidoAdmin();
    });
}

window.cambiarEstado = function (id_tmdb, tipo, nuevoEstado) {
  fetch('../Api/apiRest.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `action=guardar_cache&id_tmdb=${encodeURIComponent(id_tmdb)}&tipo=${encodeURIComponent(tipo)}&estado=${encodeURIComponent(nuevoEstado)}`
  })
    .then(res => res.json())
    .then(() => cargarContenidoAdmin());
};
