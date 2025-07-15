// auth.js — helpers para autenticación con JWT y lógica de registro/login

// Devuelve el usuario actual desde sessionStorage
function getCurrentUser() {
  const user = sessionStorage.getItem("user");
  return user ? JSON.parse(user) : null;
}

// Cierra sesión limpiando el storage y redirigiendo al login
function logout() {
  sessionStorage.clear();
  goTo("login");
}

// Redirige automáticamente si ya hay una sesión activa y se está en /login
function redirectIfLoggedIn(defaultRedirect = "admin/schedule") {
  const token = sessionStorage.getItem("jwt");
  if (token) {
    const referrer = document.referrer;
    if (!referrer || !referrer.includes(location.hostname)) {
      goTo(defaultRedirect);
    } else {
      window.location.href = referrer;
    }
  }
}

// authFetch: hace peticiones autenticadas automáticamente valida que el token este activo
async function authFetch(url, options = {}) {
  const token = sessionStorage.getItem("jwt");
  if (!token) {
    logout();
    return;
  }

  const headers = {
    ...(options.headers || {}),
    Authorization: "Bearer " + token,
    "Content-Type": "application/json",
  };

  const finalOptions = {
    method: options.method || "GET",
    headers,
    body: options.body ? JSON.stringify(options.body) : undefined,
  };

  try {
    const res = await fetch(url, finalOptions);

    if (res.status === 401) {
      logout();
      return;
    }

    if (res.status >= 500) {
      Swal.fire("Error del servidor", "Inténtalo más tarde", "error");
      return;
    }

    // Intenta parsear como JSON, atrápalo si falla
    try {
      return await res.json();
    } catch (jsonError) {
      Swal.fire("Error", "Respuesta inválida del servidor", "error");
      return;
    }
  } catch (error) {
    console.error("authFetch error:", error);
    Swal.fire("Error", "No se pudo conectar al servidor", "error");
  }
}

// initAuth: lógica para registrar e iniciar sesión
async function initAuth() {
  const fetchRegister = async () => {
    const data = {
      fullName: $("#fullName").val(),
      email: $("#email").val(),
      password: $("#password").val(),
      region: $("#region").val(),
    };

    if (!data.fullName || !data.email || !data.password || !data.region) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Por favor, completa todos los campos.",
      });
      return;
    }

    const response = await $.ajax({
      url: apiUrl("auth/register.php"),
      method: "POST",
      contentType: "application/json",
      data: JSON.stringify(data),
    });

    if (response.success) {
      Swal.fire({
        icon: "success",
        title: "Registro exitoso",
        text: "Tu cuenta ha sido creada exitosamente.",
      }).then(() => {
        "login";
      });
    } else {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: response.message || "No se pudo completar el registro.",
      });
    }
  };

  const initEvents = () => {
    $(document).on("click", "#register-button", async (e) => {
      e.preventDefault();
      fetchRegister();
    });

    $("#email-user, #password-user").on("keypress", function (e) {
      if (e.which === 13) {
        // 13 = tecla Enter
        const emailUser = $("#email-user").val().trim();
        const password = $("#password-user").val().trim();

        if (emailUser && password) {
          $("#login-button").click(); // Simula clic en el botón de login
        } else {
          alert("Por favor llena ambos campos.");
        }
      }
    });

    $(document).on("click", "#login-button", async (e) => {
      e.preventDefault();

      const credentials = {
        email: $("#email-user").val(),
        password: $("#password-user").val(),
      };

      try {
        const response = await $.ajax({
          url: apiUrl("auth/login.php"),
          method: "POST",
          contentType: "application/json",
          data: JSON.stringify(credentials),
        });

        if (response.success) {
          sessionStorage.setItem("jwt", response.token);
          sessionStorage.setItem("user", JSON.stringify(response.user));

          goTo("admin/schedule");
        } else {
          Swal.fire("Error", response.message, "error");
        }
      } catch (err) {
        Swal.fire("Error inesperado", err.message, "error");
      }
    });
  };

  try {
    initEvents();
  } catch (err) {
    console.error("Error al inicializar la autenticación:", err);
    Swal.fire("Error", "No se pudo cargar la página de registro.", "error");
  }
}
