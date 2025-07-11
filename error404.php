<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ERROR</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
    rel="stylesheet" />
  <style>
    body {
      background-image: url("public/img/bg/bg_nom035.jpg");
      background-position: center center;
      background-repeat: none;
      background-size: cover;
    }

    .display-1 {
      font-size: 12em;
      color: #328186;
    }

    .btn-primary {
      background-color: #e9775f;
      color: #fff;
      border: none;
      font-weight: 700;
      padding: 2px 20px;
      border-top-left-radius: 25px;
      border-bottom-right-radius: 25px;
    }
  </style>
</head>

<body>
  <div class="d-flex align-items-center justify-content-center vh-100">
    <div class="text-center">
      <h1 class="display-1 fw-bold">404</h1>
      <p class="fs-3">
        <span class="text-danger">Opps!</span> Página no encontrada.
      </p>
      <p class="lead">
        La pagina a la que intentas acceder esta protegida. Primero debe
        iniciar sesión
      </p>
      <a href="login" class="btn btn-primary">Iniciar Sesión</a>
      <p>Serás redirigido a la página de inicio de sesión en <span id="timer">5</span> segundos</p>
    </div>
  </div>
</body>

</html>


<script>
  let remaining = 5;
  const timerEl = document.getElementById('timer');

  const tick = () => {
    remaining--;
    timerEl.textContent = remaining;

    if (remaining === 0) {
      clearInterval(interval);
      window.location.href = 'login'; // Cambia esta URL según tu ruta real
    }
  };

  const interval = setInterval(tick, 1000);
</script>