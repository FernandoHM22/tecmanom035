async function initAdminSchedule() {
  const user = getCurrentUser();
  var userRegion = user.region;

  const hoy = new Date();
  const yyyy = hoy.getFullYear();
  const mm = String(hoy.getMonth() + 1).padStart(2, "0"); // Mes empieza desde 0
  const dd = String(hoy.getDate()).padStart(2, "0");
  const fechaActual = `${yyyy}-${mm}-${dd}`;

  $("#input-date-application").val(fechaActual);

  function fillMonthsSelect() {
    const months = [
      "Enero",
      "Febrero",
      "Marzo",
      "Abril",
      "Mayo",
      "Junio",
      "Julio",
      "Agosto",
      "Septiembre",
      "Octubre",
      "Noviembre",
      "Diciembre",
    ];

    const actualMonth = new Date().getMonth(); // 0 = Enero

    return new Promise((resolve) => {
      const $select = $("#select-month");
      $select.empty(); // Limpiar opciones previas

      $.each(months, function (index, month) {
        const selected = index === actualMonth ? "selected" : "";
        $select.append(
          `<option value="${index + 1}" ${selected}>${month}</option>`
        );
      });

      resolve(); // Puedes usar esto si quieres hacer algo después
    });
  }

  const getProjects = async () => {
    const response = await $.ajax({
      url: apiUrl("projects/getActiveProjects.php"),
      method: "POST",
      contentType: "application/json",
      dataType: "json",
      data: JSON.stringify({ region: userRegion }),
    });
    if (response.success && Array.isArray(response.data)) {
      fillProjectList(response.data);
    } else {
      Swal.fire(
        "Atención",
        response.message || "No se encontraron proyectos activos.",
        "info"
      );
    }
  };

  const fillProjectList = async (projects) => {
    projects.forEach((project) => {
      $("#client-list").append(
        `<li class="list-group-item d-flex justify-content-between align-items-center" data-id="${$.trim(
          project.ProjectID
        )}" data-headcount="${$.trim(project.Headcount)}"
        data-female-count="${$.trim(project.FemaleCount)}"
        data-male-count="${$.trim(project.MaleCount)}">${$.trim(
          project.ProjectName
        )} <span class="badge bg-primary rounded-pill">${$.trim(
          project.Headcount
        )}</span>
        </li>`
      );
    });
  };

  $(document).on("click", "#client-list li", async function (e) {
    $(this).addClass("active").siblings().removeClass("active");
    var projectId = $(this).data("id");
    var projectHeadcount = $(this).data("headcount");
    var typeGuide = "";
    if (projectHeadcount <= 50) {
      typeGuide = 2;
    } else {
      typeGuide = 3;
    }
    $("#select-guide").val(typeGuide);
  });

  const getServices = async () => {
    const response = await $.ajax({
      url: apiUrl("guides/getCategoryServices.php"),
      method: "GET",
      contentType: "application/json",
      dataType: "json",
    });
    if (response.success && Array.isArray(response.data)) {
      fillServices(response.data);
    } else {
      Swal.fire(
        "Atención",
        response.message || "No se encontraron proyectos activos.",
        "info"
      );
    }
  };

  const fillServices = async (services) => {
    const $serviceList = $("#service-list");

    services.forEach((service, index) => {
      let checked = "";
      let hiddenClass = "";
      if (
        service.CategoryID === "1" ||
        service.CategoryID === "2" ||
        service.CategoryID === "3"
      ) {
        checked = "checked";
        hiddenClass = "d-none"; // Oculta con clase
      }

      const html = `
      <label class="service-item ${hiddenClass}" for="${service.CategoryID}">
        <span class="service-label">${service.CategoryName}</span>
        <input type="checkbox" id="${service.CategoryID}" name="services[]" value="${service.CategoryName}" ${checked}>
      </label>
    `;
      $serviceList.append(html);
    });

    $serviceList.on("change", "input[type=checkbox]", function () {
      $(this).closest(".service-item").toggleClass("checked", this.checked);
    });
  };

  $(document).on("click", "#btnSample", async function (e) {
    e.preventDefault();
    const dataUrl = $(this).data("url");
    const handlerName = $(this).data("modal-handler");

    const projectElement = $("#client-list li.active");

    if (projectElement.length === 0) {
      Swal.fire("Atención", "Por favor, selecciona un proyecto.", "warning");
      return;
    }

    loadModal("schedule/" + dataUrl, {}, function (modalElement) {
      $(modalElement).find(".modal-title").text("Selección de muestra");

      if (ModalHandlers[handlerName]) {
        ModalHandlers[handlerName](modalElement, projectElement);
      } else {
        console.warn(`Handler "${handlerName}" no definido en ModalHandlers`);
      }
    });
  });

  function roundUp(value, decimals = 0) {
    const factor = Math.pow(10, decimals);
    return Math.ceil(value * factor) / factor;
  }

  $(document).on("click", "#btnCalculateSample", async function (e) {
    e.preventDefault();
    // alert("Calculando muestra...");
    const headcount = parseInt($("#input_headcount").val(), 10);
    const countFemale = parseInt($("#input_female").val(), 10);
    const countMale = parseInt($("#input_male").val(), 10);

    const sampleRaw =
      (0.9604 * headcount) / (0.0025 * (headcount - 1) + 0.9604);
    const sampleValue = roundUp(sampleRaw, 0); // redondea hacia arriba a entero

    const sampleFemaleRaw = (sampleValue / headcount) * countFemale;
    const sampleFemaleValue = roundUp(sampleFemaleRaw, 0); // redondea hacia arriba a entero

    const sampleMaleRaw = (sampleValue / headcount) * countMale;
    const sampleMaleValue = roundUp(sampleMaleRaw, 0); // redondea hacia arriba a entero

    $("#input_sample").val(sampleValue);
    $("#input_sample_female").val(sampleFemaleValue);
    $("#input_sample_male").val(sampleMaleValue);
  });

  try {
    await fillMonthsSelect();
    await getProjects();
    await getServices();
  } catch (err) {
    Swal.fire("Error", err.message, "error");
    console.error(err);
  }
}
