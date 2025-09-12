<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>¡Oops!</title>
  <style>
    body {
      margin: 0;
      height: 100vh;
      overflow: hidden;
      background: url('../assets/img/sys64mod/delete.gif') repeat center center;
      background-size: 100px 100px;
      animation: fondo-subir 30s linear infinite;
      position: relative;
    }

    @keyframes fondo-subir {
      0% { background-position: center 100%; }
      100% { background-position: center 0%; }
    }

    #castigo-img {
      position: absolute;
      bottom: -100px;
      left: 50%;
      transform: translateX(-50%);
      width: 300px;
      animation: subir-castigo 3s ease-out forwards;
      animation-delay: 5s;
      animation-fill-mode: forwards;
      z-index: 999;
      opacity: 0;
    }

    @keyframes subir-castigo {
      0% { bottom: -300px; opacity: 0; transform: translateX(-50%) scale(0.8); }
      60% { opacity: 1; }
      100% { bottom: 0px; transform: translateX(-50%) scale(1); }
    }

    .explosion-gif {
      position: absolute;
      width: 200px;
      display: none;
      z-index: 998;
    }

    #dvd-img {
      position: absolute;
      width: 600px;
      z-index: 1000;
      display: none;
      pointer-events: none;
    }
  </style>
</head>
<body>

<!-- Audios -->
<audio id="audio-main" src="../assets/img/sys64mod/catswing.mp3" autoplay loop></audio>
<audio id="audio-cas1" src="../assets/img/sys64mod/cas1.mp3"></audio>
<audio id="audio-cas2" src="../assets/img/sys64mod/cas2.mp3"></audio>
<audio id="audio-explosion" src="../assets/img/sys64mod/explosion.mp3"></audio>

<!-- Imagen castigo -->
<img id="castigo-img" src="../assets/img/sys64mod/castigo.gif" alt="Castigo">

<!-- Explosiones GIF -->
<img class="explosion-gif" src="../assets/img/sys64mod/explosion.gif" style="top: 50px; left: 100px;">
<img class="explosion-gif" src="../assets/img/sys64mod/explosion.gif" style="top: 300px; left: 400px;">
<img class="explosion-gif" src="../assets/img/sys64mod/explosion.gif" style="top: 150px; left: 700px;">

<!-- GETOUT image (DVD-style movement) -->
<img id="dvd-img" src="../assets/img/sys64mod/getout.png" alt="GET OUT">

<script>
  setTimeout(() => {
    // Reproducir todos los audios
    document.getElementById('audio-cas1').play();
    document.getElementById('audio-cas2').play();
    document.getElementById('audio-explosion').play();

    // Mostrar castigo.gif
    document.getElementById('castigo-img').style.opacity = '1';

    // Mostrar explosiones
    const explosions = document.querySelectorAll('.explosion-gif');
    explosions.forEach(img => img.style.display = 'block');

    // Activar DVD-style movimiento
    const dvd = document.getElementById('dvd-img');
    dvd.style.display = 'block';

    let posX = Math.random() * (window.innerWidth - dvd.clientWidth);
    let posY = Math.random() * (window.innerHeight - dvd.clientHeight);
    let velX = 3;
    let velY = 2;

    function animateDVD() {
      posX += velX;
      posY += velY;

      const maxX = window.innerWidth - dvd.clientWidth;
      const maxY = window.innerHeight - dvd.clientHeight;

      // Rebote horizontal
      if (posX <= 0 || posX >= maxX) {
        velX *= -1;
        changeColor();
      }
      // Rebote vertical
      if (posY <= 0 || posY >= maxY) {
        velY *= -1;
        changeColor();
      }

      dvd.style.left = posX + 'px';
      dvd.style.top = posY + 'px';

      requestAnimationFrame(animateDVD);
    }

    function changeColor() {
      dvd.style.filter = `hue-rotate(${Math.floor(Math.random() * 360)}deg)`;
    }

    animateDVD();

  }, 5000);
</script>

</body>
</html>
