$(async function () {
  const currentPage = $("body").data("page");

  if (currentPage?.startsWith("admin-")) {
    tokenCheck();
    if (typeof initSidebar === "function") initSidebar(); // activa sidebar si es necesario
    const user = getCurrentUser();
    $("#user-pill #name").text(user.name);
    $("#user-pill #userEmail").text(user.email);
    $("#user-pill #userRole").text(user.role);
  }

  switch (currentPage) {
    case "launcher":
      if (typeof initLauncher === "function") await initLauncher();
      break;

    case "forms":
      if (typeof initForms === "function") await initForms();
      break;

    case "verify-employee":
      if (typeof initVerify === "function") await initVerify();
      break;

    case "results":
      if (typeof initResults === "function") await initResults();
      break;

    case "admin-schedule":
      if (typeof initAdminSchedule === "function") await initAdminSchedule();
      break;

    case "admin-status":
      if (typeof initAdminStatus === "function") await initAdminStatus();
      break;

    case "auth-register":
      if (typeof initAuth === "function") await initAuth();
      break;

    case "auth-login":
      redirectIfLoggedIn();
      if (typeof initAuth === "function") await initAuth();
      break;

    default:
      console.warn("Página sin lógica asignada:", currentPage);
      break;
  }

  $(document).on("click", "#logoutButton", function (e) {
    e.preventDefault();
    Swal.fire({
      title: "¿Cerrar sesión?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, salir",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) logout();
    });
  });
});
