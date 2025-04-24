$(document).ready(function () {
  const divValidacion = document.getElementById("div-validacion");

  $(divValidacion).load("../../src/views/forms/validate_employee.php");

  $(document).on('keydown', '#input_cb_codigo', function (e) {
    if (e.key === 'e' || e.key === '.' || e.key === '+' || e.key === '-') {
      e.preventDefault(); // 🔒 Bloquea el carácter antes de que aparezca
    }
  });
  

  let debounceTimer; // Temporizador para debounce
  $(document).on('keyup', '#input_cb_codigo', function () {
    clearTimeout(debounceTimer); // Limpia el temporizador previo
    let cb_codigo = $(this).val().trim(); // Elimina espacios al inicio y al final

    if (cb_codigo === "") {
      return;
    }

    // Configura un temporizador que se ejecutará después de 500ms sin cambios
    debounceTimer = setTimeout(function () {
      // Ejecuta solo si hay más de 2 caracteres
      if (cb_codigo.length > 1) {
        $.ajax({
          url: "../../src/controllers/ajax/validateEmployee.php", // Archivo del servidor
          method: "POST",
          dataType: "JSON", // Tipo de datos esperados del servidor
          data: { cb_codigo: cb_codigo }, // Enviar la consulta al servidor
          success: function (response) {
            console.log(response); // Mostrar la respuesta en la consola
          },
          error: function () {
            $("#results").html("<p>Error al buscar.</p>"); // Manejar errores
          },
        });
      }
    }, 1000); // Tiempo de espera en milisegundos (500 ms)
  });
});
