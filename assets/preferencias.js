document.addEventListener('DOMContentLoaded', function () {
  // Mostrar el modal solo si el usuario no tiene preferencias
  if (typeof mostrarModalPreferencias !== 'undefined' && mostrarModalPreferencias) {
    document.getElementById('seccion-preferencias').style.display = 'block';
    document.getElementById('modal-overlay').style.display = 'block';
  }

  // Botón del header para abrir el modal manualmente
  const btnPreferencias = document.getElementById('btn-preferencias');
  if (btnPreferencias) {
    btnPreferencias.addEventListener('click', function () {
      document.getElementById('seccion-preferencias').style.display = 'block';
      document.getElementById('modal-overlay').style.display = 'block';
    });
  }

  // Botón para cerrar el modal
  const btnCerrar = document.getElementById('cerrar-preferencias');
  if (btnCerrar) {
    btnCerrar.addEventListener('click', function () {
      document.getElementById('seccion-preferencias').style.display = 'none';
      document.getElementById('modal-overlay').style.display = 'none';
    });
  }

  // Lógica para cargar y renderizar los géneros (AJAX a generos.json)
  fetch('../models/generos.json')
    .then(res => res.json())
    .then(generos => {
      const cont = document.getElementById('lista-generos');
      cont.innerHTML = '';
      generos.forEach(g => {
        const div = document.createElement('div');
        div.innerHTML = `
          <label style="display:flex;align-items:center;gap:10px;">
            <input type="checkbox" name="generos[]" value="${g.id}">
            <img src="${g.imagen}" alt="${g.nombre}" style="width:32px;height:32px;object-fit:cover;border-radius:6px;">
            ${g.nombre}
          </label>
        `;
        cont.appendChild(div);
      });
    });

  // Guardar preferencias al enviar el formulario
  const form = document.getElementById('form-preferencias');
  if (form) {
    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      const seleccionados = Array.from(document.querySelectorAll('#lista-generos input[name="generos[]"]:checked'))
        .map(cb => parseInt(cb.value));
      if (seleccionados.length === 0) {
        alert('Debes elegir al menos un género.');
        return;
      }
      const resp = await fetch('../Api/apiRest.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=guardar_preferencias&usuario_id=${encodeURIComponent(usuario_id)}&generos=${encodeURIComponent(JSON.stringify(seleccionados))}`
      });
      const data = await resp.json();
      if (data.status === 'ok') {
        alert('Preferencias guardadas correctamente.');
        document.getElementById('seccion-preferencias').style.display = 'none';
        document.getElementById('modal-overlay').style.display = 'none';
        // Opcional: recarga la página o actualiza la UI
        location.reload();
      } else {
        alert('No se pudieron guardar las preferencias.');
      }
    });
  }
});
