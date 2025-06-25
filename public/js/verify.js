async function initVerify() {
  let btnVerify = null;
  let spanMessageError = null;
  const timer = 800;
  let debounceTimer;
  let dataEmployee = null;

  const loadValidateForm = async () => {
    try {
      await loadForm(
        "employees/loadValidateForm.php",
        "#div-validacion",
        function () {
          btnVerify = document.getElementById("btnVerify");
          spanMessageError = document.getElementById("spanMessageError");
        }
      );
    } catch (error) {
      console.error(error.message);
    }
  };

  // Función para obtener información del empleado
  const getInfoEmployee = async (cb_codigo, region) => {
    try {
      const response = await $.ajax({
        url: apiUrl("employees/validateEmployee.php"),
        method: "POST",
        contentType: "application/json",
        dataType: "json",
        data: JSON.stringify({ cb_codigo, region }),
      });

      if (response.success) {
        if (response.data.estatus && response.data.estatus === "N") {
          await Swal.fire({
            icon: "warning",
            title: "Empleado inactivo",
            text: "El empleado no está habilitado para realizar el cuestionario. Revise el número de empleado.",
          });

          $('input[id^="input_"]:not(#input_cb_codigo)').val("");
          return;
        }

        dataEmployee = response.data;

        $.each(response.data, (key, value) => {
          $("#input_" + key).val(value);
        });

        $(btnVerify).prop("disabled", false);

        if ($(spanMessageError).html() !== "") {
          $(spanMessageError).empty();
        }
      } else {
        $(spanMessageError)
          .html(
            `<i class="fa-solid fa-circle-exclamation me-1"></i> ${response.message}`
          )
          .css({
            color: "darkred",
            fontWeight: "700",
            fontSize: "0.8rem",
          });

        $('input[id^="input_"]:not(#input_cb_codigo)').val("");
        $(btnVerify).prop("disabled", true);
      }
    } catch (error) {
      console.error("Error en la comunicación:", error);
      $("#results").html("<p>Error al buscar.</p>");
    }
  };

  // Función para inicializar eventos
  const initializeEvents = () => {
    $(document).on("change", "#select_region", function (e) {
      e.preventDefault();
      const region = $(this).val();

      if (region) {
        $("#input_cb_codigo").prop("disabled", false);
      }
      $("input.form-control").val("");
      $(btnVerify).prop("disabled", true);
    });

    $(document).on("keydown", "#input_cb_codigo", (e) => {
      if (["e", ".", "+", "-"].includes(e.key)) {
        e.preventDefault();
      }
    });

    $(document).on("input", "#input_cb_codigo", function () {
      const value = $(this).val().trim();
      if (value === "" && $(spanMessageError).html().trim() !== "") {
        $(spanMessageError).empty();
      }
    });

    $(document).on("keyup", "#input_cb_codigo", function () {
      clearTimeout(debounceTimer);
      const cb_codigo = $(this).val().trim();
      const region = $("#select_region").val().trim();

      if (cb_codigo !== "") {
        debounceTimer = setTimeout(() => {
          if (cb_codigo.length >= 1) {
            getInfoEmployee(cb_codigo, region);
          }
        }, timer);
      }else{
        $('input[type="text"]:not(#input_cb_codigo)').val("");
      }
    });

    $(document).on("click", "#btnVerify", async (e) => {
      e.preventDefault();
      const cb_codigo = $("#input_cb_codigo").val().trim();
      if (!cb_codigo) return;

      const result = await Swal.fire({
        title: "¿Está seguro de iniciar?",
        text: "Verifique que su información sea correcta antes de comenzar el cuestionario.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, iniciar Cuestionario",
        cancelButtonText: "Cancelar",
      });

      if (result.isConfirmed) {
        // window.location.href = `${BASE_URL}/forms?project=AMCOR`;
        dataEmployee = await completeInfoEmployee(cb_codigo); // asegúrate que retorna un objeto
        sessionStorage.setItem("dataEmployee", JSON.stringify(dataEmployee));
        // var projectID = dataEmployee.proyecto_id || "AMCOR"; // Asignar un valor por defecto si no existe
        window.location.href = `forms`;
      }
    });

    const completeInfoEmployee = async () => {
      const employee = dataEmployee; // Asumiendo que ya tienes `dataEmployee` cargado

      const parseEdad = (edadStr) => {
        const match = edadStr.match(/\d+/);
        return match ? parseInt(match[0], 10) : null;
      };

      const parseAntiguedad = (antiguedadStr) => {
        const mesesMatch = antiguedadStr.match(/(\d+)\s*mes/);
        const aniosMatch = antiguedadStr.match(/(\d+)\s*años?/);

        let totalMeses = 0;
        if (aniosMatch) totalMeses += parseInt(aniosMatch[1]) * 12;
        if (mesesMatch) totalMeses += parseInt(mesesMatch[1]);

        return totalMeses;
      };

      const getRangoEdad = (edad) => {
        if (edad >= 18 && edad <= 29) return "De 18 a 29 años";
        if (edad >= 30 && edad <= 44) return "De 30 a 44 años";
        if (edad >= 45 && edad <= 59) return "De 45 a 59 años";
        if (edad >= 60) return "60 o más años";
        return "Sin definir";
      };

      const getRangoAntiguedad = (meses) => {
        if (meses <= 3) return "De 0 a 3 meses";
        if (meses >= 4 && meses <= 6) return "De 4 a 6 meses";
        if (meses >= 7 && meses <= 11) return "De 7 a 11 meses";
        if (meses >= 12 && meses <= 36) return "De 1 a 3 años";
        if (meses >= 48 && meses <= 120) return "De 4 a 10 años";
        if (meses >= 132 && meses <= 180) return "De 11 a 15 años";
        if (meses >= 192) return "16 o más años";
        return "Sin definir";
      };

      const edad = parseEdad(employee.edad); // "29 años" -> 29
      const mesesAntiguedad = parseAntiguedad(employee.antiguedad); // "6 años" -> 72

      employee.rango_edad = getRangoEdad(edad);
      employee.rango_antiguedad = getRangoAntiguedad(mesesAntiguedad);

      return employee;
    };
  };

  await loadValidateForm();
  initializeEvents();
}
