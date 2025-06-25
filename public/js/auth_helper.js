//HELPER para validar la existencia de un token en vistas para prevenir el ingreso indebido sin un inicio de sesion
async function tokenCheck(redirectIfMissing = "error404") {
  const token = sessionStorage.getItem("jwt");

  if (!token) {
    window.location.href = BASE_URL + "/" + redirectIfMissing;
    return;
  }

  // Evitar redirección infinita
  if (
    window.location.search.includes("tokenCheck") ||
    localStorage.getItem("tokenVerified")
  ) {
    localStorage.removeItem("tokenVerified");
    document.body.classList.remove("protected"); // ✅ Mostrar contenido tras validación
    return;
  }

  // Marcar que ya se está validando el token
  localStorage.setItem("tokenVerified", "true");

  fetch(window.location.pathname + "?tokenCheck", {
    method: "GET",
    headers: {
      Authorization: "Bearer " + token,
    },
  })
    .then((res) => {
      if (!res.ok) {
        sessionStorage.clear();
        localStorage.removeItem("tokenVerified");
        window.location.href = BASE_URL + "/" + redirectIfMissing;
        document.body.classList.remove("protected"); // ✅ Mostrar contenido tras validación exitosa
      } else {
        // ✅ Ya validado, ahora recarga con la bandera presente
        location.reload();
      }
    })
    .catch(() => {
      sessionStorage.clear();
      localStorage.removeItem("tokenVerified");
      window.location.href = BASE_URL + "/" + redirectIfMissing;
    });
}
