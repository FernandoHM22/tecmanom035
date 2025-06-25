function apiUrl(endpoint) {
  return BASE_URL + "/public/ajax/" + endpoint.replace(/^\/+/, "");
}

// Función para cargar formularios dinámicamente soportando subcarpetas
function loadForm(formPath, targetSelector, callback = null) {
  const normalizedPath = formPath.replace(/^\/+/, ""); // Quitar slashes iniciales si existen
  $(targetSelector).load(
    `${BASE_URL}/public/ajax/${normalizedPath}`,
    function () {
      if (typeof callback === "function") {
        callback();
      }
    }
  );
}

function goTo(relativePath) {
  const normalizedPath = "/" + relativePath.replace(/^\/+/, "");
  const fullUrl = BASE_URL + normalizedPath;
  window.location.href = fullUrl;
}

// Cargar un modal dinámicamente desde la carpeta 'views/modals/'
function loadModal(modalNameWithPath, params = {}, callback = null) {
  const queryString = new URLSearchParams({
    modal: modalNameWithPath,
    ...params,
  }).toString();
  const url = `${BASE_URL}/public/ajax/modal_loader.php?${queryString}`;

  $.get(url, function (html) {
    $("#mainModalContainer").html(html);
    const $modal = $("#mainModalContainer .modal").first();
    if ($modal.length) {
      const modalInstance = new bootstrap.Modal($modal[0]);
      modalInstance.show();
      if (typeof callback === "function") callback($modal[0]);
    }
  });
}

// Limpieza automática al cerrar cualquier modal
$("#mainModalContainer").on("hidden.bs.modal", ".modal", function () {
  $(this).remove();
});
