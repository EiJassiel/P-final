document.addEventListener('DOMContentLoaded', () => {
  const btnCambiarTema = document.getElementById('btn-cambiar-tema');
  const body = document.getElementById('body');

  function setCookie(nombre, valor, dias) {
    const d = new Date();
    d.setTime(d.getTime() + (dias * 24 * 60 * 60 * 1000));
    const expires = "expires=" + d.toUTCString();
    document.cookie = nombre + "=" + valor + ";" + expires + ";path=/";
  }

  function actualizarTextoBoton() {
    if (body.classList.contains('tema-oscuro')) {
      btnCambiarTema.textContent = 'Modo claro';
    } else {
      btnCambiarTema.textContent = 'Modo oscuro';
    }
  }

  btnCambiarTema.addEventListener('click', () => {
    if (body.classList.contains('tema-oscuro')) {
      body.classList.remove('tema-oscuro');
      body.classList.add('tema-claro');
      setCookie('tema', 'claro', 30);
    } else {
      body.classList.remove('tema-claro');
      body.classList.add('tema-oscuro');
      setCookie('tema', 'oscuro', 30);
    }
    actualizarTextoBoton();
  });

  // Inicializar texto al cargar
  actualizarTextoBoton();
});
