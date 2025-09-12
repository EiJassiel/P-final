// LOGIN Y REGISTRO CENTRALIZADO
// Este script gestiona el envío de formularios de login y registro, validando y mostrando errores.

document.addEventListener('DOMContentLoaded', function() {
  // LOGIN
  const loginForm = document.getElementById('formLogin');
  if (loginForm) {
  loginForm.addEventListener('submit', function(event) {
    event.preventDefault();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const loginError = document.getElementById('login-error');
    if (loginError) loginError.style.display = 'none';

    if (!email || !password) {
      if (loginError) {
        loginError.textContent = 'Por favor, completa todos los campos';
        loginError.style.display = 'block';
      }
      return;
    }

    fetch('../Api/apiRest.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `action=login&correo=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
    })
    .then(response => response.json())
    .then(data => {
      console.log('Login response:', data); // ← para depurar qué llega

      if (data.status === 'ok') {
        // Guardar usuario en sesión vía petición a PHP
        fetch('setSession.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `id=${encodeURIComponent(data.id)}&nombre=${encodeURIComponent(data.nombre)}&correo=${encodeURIComponent(email)}&tipo=${encodeURIComponent(data.tipo)}`
        })
        .then(() => {
          if (data.tipo === 'admin') {
            window.location.href = 'dasboardAdmin.php';
          } else {
            window.location.href = 'home.php';
          }
        });

      } else if (data.status === 'lock') {
        // Mostrar mensaje de bloqueo temporal por muchos intentos
        if (loginError) {
          loginError.textContent = 'Has superado el número de intentos. Espera unos minutos.';
          loginError.style.display = 'block';
        }

      } else if (data.status === 'castigo') {
        // Redirigir a la página castigo.php
        window.location.href = '../vistas/castigo.php';

      } else {
        // Usuario o contraseña incorrectos
        if (loginError) {
          loginError.textContent = 'Usuario o contraseña incorrectos';
          loginError.style.display = 'block';
        }
      }
    })
    .catch(() => {
      if (loginError) {
        loginError.textContent = 'Error de conexión con el servidor';
        loginError.style.display = 'block';
      }
    });
  });
}


  // REGISTRO
  const registerForm = document.getElementById('formRegister');
  if (registerForm) {
    registerForm.addEventListener('submit', function(event) {
      event.preventDefault();
      const registerError = document.getElementById('register-error');
      if (registerError) registerError.style.display = 'none';
      const username = registerForm.querySelector('input[name="username"]').value.trim();
      const email = registerForm.querySelector('input[name="email"]').value.trim();
      const password = registerForm.querySelector('input[name="password"]').value.trim();
      const password2 = registerForm.querySelector('input[name="password2"]').value.trim();

      // Validaciones
      if (!username || !email || !password || !password2) {
        if (registerError) {
          registerError.textContent = 'Por favor, completa todos los campos';
          registerError.style.display = 'block';
        }
        return;
      }
      if (!/^[a-zA-Z0-9_]{3,20}$/.test(username)) {
        if (registerError) {
          registerError.textContent = 'El usuario debe tener entre 3 y 20 caracteres alfanuméricos o guion bajo.';
          registerError.style.display = 'block';
        }
        return;
      }
      if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
        if (registerError) {
          registerError.textContent = 'El correo no es válido.';
          registerError.style.display = 'block';
        }
        return;
      }
      if (password.length < 6) {
        if (registerError) {
          registerError.textContent = 'La contraseña debe tener al menos 6 caracteres.';
          registerError.style.display = 'block';
        }
        return;
      }
      if (password !== password2) {
        if (registerError) {
          registerError.textContent = 'Las contraseñas no coinciden.';
          registerError.style.display = 'block';
        }
        return;
      }

      fetch('../Api/apiRest.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=registro&usuario=${encodeURIComponent(username)}&correo=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          if (registerError) {
            registerError.style.display = 'none';
          }
          alert('Registro exitoso. Ahora puedes iniciar sesión.');
          window.location.href = '../vistas/login.php';
        } else {
          if (registerError) {
            registerError.textContent = data.msg || 'Error al registrar el usuario';
            registerError.style.display = 'block';
          }
        }
      })
      .catch(() => {
        if (registerError) {
          registerError.textContent = 'Error de conexión con el servidor';
          registerError.style.display = 'block';
        }
      });
    });
  }
});






