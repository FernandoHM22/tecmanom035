async function initAdminSchedule() {
  const user = getCurrentUser();
  const userRegion = user.region;
  const userEmail = user.email;

  const hoy = new Date();
  const yyyy = hoy.getFullYear();
  const mm = String(hoy.getMonth() + 1).padStart(2, "0"); // Mes empieza desde 0
  const dd = String(hoy.getDate()).padStart(2, "0");
  const currentDate = `${yyyy}-${mm}-${dd}`;
  let lastProjectIdForShifts = null;
  let lastProjectIdForSupervisors = null;
  let areasSampleData = [];
  let shiftsSampleData = [];
  let supervisorsSampleData = [];
  let fuse; // Variable global
  let fullProjectList = []; // Aquí guardaremos la lista completa

  let saveSurveyShownOnce = false;

  $("#input-date-application").val(currentDate);

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
    const url = apiUrl("projects/getActiveProjects.php");

    const response = await authFetch(url, {
      method: "POST",
      body: { region: userRegion },
    });

    if (!response) return; // authFetch ya maneja logout y errores

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
    fullProjectList = projects; // Guardamos copia completa para búsqueda
    renderProjectList(projects); // Mostrar todo al inicio

    // Configurar Fuse una sola vez
    const options = {
      keys: ["ProjectName"],
      threshold: 0.4, // Puedes ajustar la sensibilidad
    };
    fuse = new Fuse(projects, options);
  };

  const renderProjectList = (projects) => {
    $("#client-list").empty();
    projects.forEach((project) => {
      var surveyActive = project.SurveyActive ? "survey-active" : "";
      $("#client-list").append(
        `<li class="list-group-item d-flex justify-content-between align-items-center ${surveyActive}" data-id="${$.trim(
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

  $(document).on(
    "dblclick",
    "#client-list li.survey-active",
    async function (e) {
      e.preventDefault();
      goTo("/admin/status");
    }
  );

  $(document).on("click", "#client-list li", async function (e) {
    e.preventDefault();
    if ($(this).hasClass("active")) return;

    $(this).addClass("active").siblings().removeClass("active");
    const projectHeadcount = $(this).attr("data-headcount");
    const typeGuide = projectHeadcount <= 50 ? 2 : 3;
    const projectName = $(this).clone().children().remove().end().text().trim();

    await getLogo(projectName);

    $("#select-guide").val(typeGuide);

    $("#generalSampleFactorContainer")
      .removeClass("d-block")
      .addClass("d-none");
    $("#generalSampleFactor").text("");
    $("#sampleTabsContent  table tbody").empty();

    $("#input_gte").attr("disabled", false);

    $("#sampleTabs .nav-link").removeClass("active");
    $("#sampleTabs .nav-link:first").addClass("active");

    $("#sampleTabsContent .tab-pane").removeClass("active show");
    $("#sampleTabsContent .tab-pane:first").addClass("active show");
    $("#btnSample").prop("disabled", false);
    checkIfReadyToSave();
  });

  $(document).on("input", "#searchProject", function () {
    const search = $(this).val().trim();

    if (search === "") {
      renderProjectList(fullProjectList); // Restaurar lista completa
    } else {
      const result = fuse.search(search).map((r) => r.item);
      renderProjectList(result);
    }
  });

  const getLogo = async (projectName) => {
    const url = apiUrl("projects/getProjectLogo.php");
    const response = await authFetch(url, {
      method: "POST",
      body: { projectName }, // authFetch se encarga de hacer JSON.stringify
    });
    if (!response) return; // si authFetch devuelve null o undefined, abortamos
    if (response) {
      const $logosContainer = $("#logos-container");
      // Verifica si el contenedor de logos está oculto y lo muestra
      if ($logosContainer.hasClass("d-none")) {
        $logosContainer.removeClass("d-none").addClass("d-block");
      }
      const $logo = $("#project-logo");
      $logo.attr("src", response.logo);
    } else {
      Swal.fire({
        toast: true,
        position: "top",
        icon: "info",
        title:
          "No se encontró el logo del proyecto. Vuelve a intentarlo. Si el problema persiste, contacta al administrador.",
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
      });
    }
  };

  $(document).on(
    "change",
    "#logos-container input[type=radio]",
    async function (e) {
      e.preventDefault();
      if ($(this).val() === "no") {
        const dataUrl = $(this).data("url");
        const handlerName = $(this).data("modal-handler");
        const projectElement = $("#client-list li.active");

        loadModal("schedule/" + dataUrl, {}, function (modalElement) {
          $(modalElement).find(".modal-title").text("Cambio de logotipo");

          if (ModalHandlers[handlerName]) {
            ModalHandlers[handlerName](modalElement, projectElement);
            // setTimeout(async () => {
            //   await calculateSample();
            // }, 500);
          } else {
            console.warn(
              `Handler "${handlerName}" no definido en ModalHandlers`
            );
          }
        });
      }
    }
  );

  $(document).on("click", "#confirm-new-logo", function () {
    const file = $("#new-logo")[0].files[0];
    if (file) {
      alert('Archivo "' + file.name + '" listo para cargar.');
      // Aquí podrías hacer una carga AJAX si lo necesitas
    } else {
      alert("Por favor selecciona un archivo PNG primero.");
    }
  });

  const getServices = async () => {
    const url = apiUrl("guides/getCategoryServices.php");

    const response = await authFetch(url, {
      method: "GET",
    });

    if (!response) return; // si authFetch devuelve null o undefined, abortamos

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
        <input type="checkbox" id="${service.CategoryID}" name="services[]" value="${service.CategoryID}" ${checked}>
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
        setTimeout(async () => {
          await calculateSample();
        }, 500);
      } else {
        console.warn(`Handler "${handlerName}" no definido en ModalHandlers`);
      }
    });
  });

  function roundUp(value, decimals = 0) {
    const factor = Math.pow(10, decimals);
    return Math.ceil(value * factor) / factor;
  }

  async function calculateSample() {
    var headcount = parseInt($("#input_headcount").val(), 10);
    var countFemale = parseInt($("#input_female").val(), 10);
    var countMale = parseInt($("#input_male").val(), 10);
    const confirmButton = $("#modalSample #btnConfirmSample");

    const sampleRaw =
      (0.9604 * headcount) / (0.0025 * (headcount - 1) + 0.9604);
    var sampleValue = roundUp(sampleRaw, 0); // redondea hacia arriba a entero

    var sampleFemaleRaw = (sampleValue / headcount) * countFemale;
    var sampleFemaleValue = roundUp(sampleFemaleRaw, 0); // redondea hacia arriba a entero

    var sampleMaleRaw = (sampleValue / headcount) * countMale;
    var sampleMaleValue = roundUp(sampleMaleRaw, 0); // redondea hacia arriba a entero

    if (headcount < 60) {
      // Si el headcount es menor a 60, la muestra es igual al headcount
      sampleValue = headcount;
      sampleFemaleValue = countFemale;
      sampleMaleValue = countMale;
    }
    $("#input_sample").val(sampleValue);
    $("#input_sample_female").val(sampleFemaleValue);
    $("#input_sample_male").val(sampleMaleValue);

    confirmButton.removeAttr("disabled"); // Habilita el botón de confirmar muestra
  }

  $(document).on("click", "#btnConfirmSample", async function (e) {
    e.preventDefault();

    var sampleValue = $("#input_sample").val();
    var sampleFemaleValue = $("#input_sample_female").val();
    var sampleMaleValue = $("#input_sample_male").val();

    var projectElement = $("#client-list li.active");
    var projectId = $(projectElement).attr("data-id");

    var percentageGeneralSampleFactor = roundUp(
      (sampleValue / parseInt($(projectElement).attr("data-headcount"), 10)) *
        100,
      0
    );

    var generalSampleFactor = percentageGeneralSampleFactor / 100;

    $("#generalSampleFactorContainer")
      .removeClass("d-none")
      .addClass("d-block");
    $("#generalSampleFactor").text(percentageGeneralSampleFactor + "%");

    localStorage.setItem("generalSampleFactor", generalSampleFactor);
    localStorage.setItem("sampleValue", sampleValue);
    localStorage.setItem("sampleFemaleValue", sampleFemaleValue);
    localStorage.setItem("sampleMaleValue", sampleMaleValue);
    const areasProject = await getAreasByProject(projectId);

    await fillAreasSampleTable(areasProject);
  });

  $(document).on("change", "input[name='services[]']", function () {
    checkIfReadyToSave();
  });

  $(document).on("change", "#input_gte", function () {
    let text = $(this).val().trim();
    let capitalizedText = text.toLowerCase().replace(/\b\w/g, function (char) {
      return char.toUpperCase();
    });

    $(this).val(capitalizedText);

    checkIfReadyToSave();
  });

  const getAreasByProject = async (projectId) => {
    const url = apiUrl("projects/getAreasByProject.php");
    const response = await authFetch(url, {
      method: "POST",
      body: { projectId, region: userRegion }, // authFetch se encarga de hacer JSON.stringify
    });
    if (!response) return; // si authFetch devuelve null o undefined, abortamos

    if (response.success && Array.isArray(response.data)) {
      return response.data;
    } else {
      Swal.fire({
        toast: true,
        position: "bottom",
        icon: "info",
        title:
          "No se encontraron áreas para el proyecto seleccionado. Vuelve a intentarlo. Si el problema persiste, contacta al administrador.",
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.style.margin = "0 auto";
          toast.style.bottom = "20px";
        },
      });

      return;
    }
  };

  const getShiftsByProject = async (projectId) => {
    const url = apiUrl("projects/getShiftsByProject.php");
    const response = await authFetch(url, {
      method: "POST",
      body: { projectId, region: userRegion }, // authFetch se encarga de hacer JSON.stringify
    });
    if (!response) return; // si authFetch devuelve null o undefined, abortamos

    if (response.success && Array.isArray(response.data)) {
      return response.data;
    } else {
      Swal.fire({
        toast: true,
        position: "bottom",
        icon: "info",
        title:
          "No se encontraron turnos para el proyecto seleccionado. Vuelve a intentarlo. Si el problema persiste, contacta al administrador.",
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.style.margin = "0 auto";
          toast.style.bottom = "20px";
        },
      });

      return;
    }
  };

  const getSupervisorsByProject = async (projectId) => {
    const url = apiUrl("projects/getSupervisorsByProject.php");
    const response = await authFetch(url, {
      method: "POST",
      body: { projectId, region: userRegion }, // authFetch se encarga de hacer JSON.stringify
    });
    if (!response) return; // si authFetch devuelve null o undefined, abortamos

    if (response.success && Array.isArray(response.data)) {
      return response.data;
    } else {
      Swal.fire({
        toast: true,
        position: "bottom",
        icon: "info",
        title:
          "No se encontraron supervisores para el proyecto seleccionado. Vuelve a intentarlo. Si el problema persiste, contacta al administrador.",
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.style.margin = "0 auto";
          toast.style.bottom = "20px";
        },
      });

      return;
    }
  };

  async function fillAreasSampleTable(areas) {
    const $table = $("#tableAreasSample tbody");
    $table.empty();
    areasSampleData = [];

    const factor = parseFloat(localStorage.getItem("generalSampleFactor"));

    for (const area of areas) {
      let MaleCount = Math.round(area.Hombres * factor);
      let FemaleCount = Math.round(area.Mujeres * factor);

      areasSampleData.push({
        Area: area.Area,
        Colaboradores: area.Colaboradores,
        Hombres: MaleCount,
        Mujeres: FemaleCount,
      });

      const row = `
        <tr>
          <td title="${area.Area}">${area.Area}</td>
          <td>${area.Colaboradores}</td>
          <td>${MaleCount}</td>
          <td>${FemaleCount}</td>
        </tr>
      `;
      $table.append(row);
    }
    // Actualiza el estado de la pestaña de áreas
    $(".nav-link.active").removeClass("unopened-tab").addClass("opened-tab");
    $(".nav-link.active").prepend(
      '<span class="check-icon"><img class="icon me-0" src="' +
        BASE_URL +
        '/public/img/icons/check-circle.svg" alt="Check Icon"></span>'
    );
    checkIfReadyToSave();
  }

  async function fillShiftsSampleTable(shifts) {
    const $table = $("#tableShiftsSample tbody");
    $table.empty();
    shiftsSampleData = [];

    const factor = parseFloat(localStorage.getItem("generalSampleFactor"));

    for (const shift of shifts) {
      let MaleCount = Math.round(shift.Hombres * factor);
      let FemaleCount = Math.round(shift.Mujeres * factor);

      shiftsSampleData.push({
        Turno: shift.Turno,
        Colaboradores: shift.Colaboradores,
        Hombres: MaleCount,
        Mujeres: FemaleCount,
      });

      const row = `
        <tr>
          <td title="${shift.Turno}">${shift.Turno}</td>
          <td>${shift.Colaboradores}</td>
          <td>${MaleCount}</td>
          <td>${FemaleCount}</td>
        </tr>
      `;
      $table.append(row);
    }
    checkIfReadyToSave();
  }

  async function fillSupervisorsSampleTable(supervisors) {
    const $table = $("#tableSupervisorsSample tbody");
    $table.empty(); // Limpiar tabla antes de rellenar
    supervisorsSampleData = []; // Reiniciar datos cada vez

    const factor = parseFloat(localStorage.getItem("generalSampleFactor"));

    for (const supervisor of supervisors) {
      let MaleCount = Math.round(supervisor.Hombres * factor);
      let FemaleCount = Math.round(supervisor.Mujeres * factor);

      supervisorsSampleData.push({
        Supervisor: supervisor.Supervisor,
        Colaboradores: supervisor.Colaboradores,
        Hombres: MaleCount,
        Mujeres: FemaleCount,
      });

      const row = `
        <tr>
          <td title="${supervisor.Supervisor}">${supervisor.Supervisor}</td>
          <td>${supervisor.Colaboradores}</td>
          <td>${MaleCount}</td>
          <td>${FemaleCount}</td>
        </tr>
      `;
      $table.append(row);
    }
    checkIfReadyToSave();
  }

  // $(document).on("click", "#shifts-tab", async function (e) {
  //   const projectElement = $("#client-list li.active");
  //   const projectId = projectElement.attr("data-id");

  //   if (!projectId) {
  //     Swal.fire("Atención", "Por favor, selecciona un proyecto.", "warning");
  //     e.preventDefault(); // evita que Bootstrap cambie de pestaña
  //     return;
  //   }

  //   // Evita recargar si es el mismo proyecto que ya se cargó
  //   if (lastProjectIdForShifts === projectId) return;

  //   if ($("#tableAreasSample tbody").children().length === 0) {
  //     Swal.fire(
  //       "Atención",
  //       "Primero debe generar la muestra del proyecto.",
  //       "warning"
  //     );
  //     return;
  //   }

  //   const shiftsProject = await getShiftsByProject(projectId);
  //   if (!shiftsProject) return;

  //   await fillShiftsSampleTable(shiftsProject);
  //   lastProjectIdForShifts = projectId;
  // });

  // $(document).on("click", "#supervisors-tab", async function (e) {
  //   const projectElement = $("#client-list li.active");
  //   const projectId = projectElement.attr("data-id");

  //   if (!projectId) {
  //     Swal.fire("Atención", "Por favor, selecciona un proyecto.", "warning");
  //     return;
  //   }

  //   if (lastProjectIdForSupervisors === projectId) return;

  //   if ($("#tableAreasSample tbody").children().length === 0) {
  //     e.preventDefault();

  //     Swal.fire(
  //       "Atención",
  //       "Primero debe generar la muestra del proyecto.",
  //       "warning"
  //     );
  //     return;
  //   }

  //   const supervisorsProject = await getSupervisorsByProject(projectId);
  //   if (!supervisorsProject) return;

  //   await fillSupervisorsSampleTable(supervisorsProject);
  //   lastProjectIdForSupervisors = projectId;
  // });

  $('button[data-bs-toggle="tab"]').on("show.bs.tab", async function (e) {
    const $tab = $(this);
    const tabId = $(this).attr("id");
    const projectElement = $("#client-list li.active");
    const projectId = projectElement.attr("data-id");

    if (!projectId) {
      e.preventDefault();
      Swal.fire("Atención", "Por favor, selecciona un proyecto.", "warning");
      return;
    }

    if ($("#tableAreasSample tbody").children().length === 0) {
      e.preventDefault();
      Swal.fire(
        "Atención",
        "Primero debe generar la muestra del proyecto.",
        "warning"
      );
      return;
    }

    // Mapeo de comportamiento por tab
    const handlers = {
      "shifts-tab": {
        lastProjectId: lastProjectIdForShifts,
        fetchData: getShiftsByProject,
        fillTable: fillShiftsSampleTable,
        updateLastId: (id) => (lastProjectIdForShifts = id),
      },
      "supervisors-tab": {
        lastProjectId: lastProjectIdForSupervisors,
        fetchData: getSupervisorsByProject,
        fillTable: fillSupervisorsSampleTable,
        updateLastId: (id) => (lastProjectIdForSupervisors = id),
      },
    };

    const handler = handlers[tabId];
    if (!handler) return; // no hacemos nada si es otro tab

    if (handler.lastProjectId === projectId) return;

    const data = await handler.fetchData(projectId);
    if (!data) {
      e.preventDefault(); // evita cambiar tab si no hay datos válidos
      return;
    }

    await handler.fillTable(data);
    handler.updateLastId(projectId);

    $tab.removeClass("unopened-tab").addClass("opened-tab");

    // Añadir ícono solo si no existe ya
    if ($tab.find(".check-icon").length === 0) {
      $tab.prepend(
        '<span class="check-icon"><img class="icon me-0" src="' +
          BASE_URL +
          '/public/img/icons/check-circle.svg" alt="Check Icon"></span>'
      );
    }
  });

  function checkIfReadyToSave() {
    const gteValue = $("#input_gte").val()?.trim();
    const selectedServices = $("input[name='services[]']:checked").length;
    const areasFilled = $("#tableAreasSample tbody").children().length;
    const shiftsFilled = $("#tableShiftsSample tbody").children().length;
    const supervisorsFilled = $("#tableSupervisorsSample tbody").children()
      .length;
    const logo = $("#logos-container input[type=radio]:checked").val();

    const ready =
      gteValue !== "" &&
      selectedServices > 3 &&
      areasFilled > 0 &&
      shiftsFilled > 0 &&
      supervisorsFilled > 0 &&
      logo === "yes";

    if (ready) {
      $("#btnSaveSurvey").fadeIn();

      if (!saveSurveyShownOnce) {
        saveSurveyShownOnce = true;
        $("html, body").animate(
          {
            scrollTop: $("#btnSaveSurvey").offset(),
          },
          500,
          function () {
            $("#btnSaveSurvey").focus();
            $("#btnSaveSurvey").addClass("breathe-once");
            setTimeout(() => {
              $("#btnSaveSurvey").removeClass("breathe-once");
            }, 600);
          }
        );
      }
    } else {
      $("#btnSaveSurvey").fadeOut();
      saveSurveyShownOnce = false;
    }

    return ready;
  }

  $(document).on("click", "#btnSaveSurvey", async function (e) {
    e.preventDefault();

    const isReady = checkIfReadyToSave();
    if (!isReady) return;

    const surveyConfig = {
      projectId: $("#client-list li.active").attr("data-id"),
      dateApplication: $("#input-date-application").val(),
      guide: $("#select-guide").val(),
    };

    const dataHR = {
      projectId: $("#client-list li.active").attr("data-id"),
      gte: $("#input_gte").val(),
    };

    const selectedServices = {
      projectId: $("#client-list li.active").attr("data-id"),
      createdBy: userEmail,
      CategoryID: $("input[name='services[]']:checked")
        .map(function () {
          return $(this).val();
        })
        .get(),
    };

    const sampleGeneral = {
      sampleValue: parseInt(localStorage.getItem("sampleValue"), 10),
      generalSampleFactor: parseFloat(
        localStorage.getItem("generalSampleFactor")
      ),
      sampleMaleValue: parseInt(localStorage.getItem("sampleMaleValue"), 10),
      sampleFemaleValue: parseInt(
        localStorage.getItem("sampleFemaleValue"),
        10
      ),
    };

    const sampleData = {
      projectId: $("#client-list li.active").attr("data-id"),
      sampleGeneral: sampleGeneral,
      sampleAreas: areasSampleData,
      sampleShifts: shiftsSampleData,
      sampleSupervisors: supervisorsSampleData,
    };

    try {
      const res1 = await saveSurveyConfig(surveyConfig);
      if (!res1?.success)
        throw new Error(
          "Error al guardar configuración general de la encuesta."
        );

      const res2 = await saveRHData(dataHR);
      if (!res2?.success)
        throw new Error("Error al guardar información de Recursos Humanos.");

      const res3 = await saveServices(selectedServices);
      if (!res3?.success)
        throw new Error("Error al guardar servicios seleccionados.");

      const res4 = await saveSampleData(sampleData);
      if (!res4?.success)
        throw new Error("Error al guardar datos de muestreo.");

      Swal.fire(
        "Guardado exitoso",
        "Todos los datos se guardaron correctamente.",
        "success"
      );

      await resetForm();
    } catch (err) {
      Swal.fire("Error", err.message, "error");
      console.error(err);
    }
  });

  const saveSurveyConfig = async (surveyConfig) => {
    const url = apiUrl("schedule/saveSurveyConfig.php");

    const response = await authFetch(url, {
      method: "POST",
      body: { surveyConfig },
    });

    if (!response) return; // authFetch ya maneja logout y errores
    return response; // <-- esto es importante
  };

  const saveRHData = async (dataHR) => {
    const url = apiUrl("schedule/saveRHData.php");

    const response = await authFetch(url, {
      method: "POST",
      body: { dataHR },
    });

    if (!response) return; // authFetch ya maneja logout y errores
    return response; // <-- esto es importante
  };

  const saveServices = async (selectedServices) => {
    const url = apiUrl("schedule/saveServices.php");

    const response = await authFetch(url, {
      method: "POST",
      body: { selectedServices },
    });

    if (!response) return; // authFetch ya maneja logout y errores
    return response; // <-- esto es importante
  };

  const saveSampleData = async (sampleData) => {
    const url = apiUrl("schedule/saveSampleData.php");

    const response = await authFetch(url, {
      method: "POST",
      body: { sampleData },
    });

    if (!response) return; // authFetch ya maneja logout y errores
    return response; // <-- esto es importante
  };

  async function resetForm() {
    $("#btnSaveSurvey").fadeOut(); // Oculta el botón después de guardar
    $("#client-list li.active").removeClass("active"); // Limpia la selección del proyecto
    $("#logos-container").removeClass("d-block").addClass("d-none"); // Oculta el contenedor de logos
    $("#project-logo").attr("src", ""); // Limpia el logo del proyecto
    $("#logos-container input[type=radio][value='yes']").prop("checked", true); // Marca el logo por defecto
    $("#input-date-application").val(currentDate); // Resetea la fecha al día actual
    $("#select-guide").val(""); // Resetea la guía al valor por defecto
    $("#input_gte").val(""); // Resetea el campo GTE
    $("#generalSampleFactor").html(""); // Limpia el factor de muestra general
    $("#service-list input[type=checkbox]").prop("checked", false); // Desmarca todos los servicios
    $("#service-list label.service-item.checked").removeClass("checked");
    $("#tableAreasSample tbody").empty(); // Limpia la tabla de áreas
    $("#tableShiftsSample tbody").empty(); // Limpia la tabla de turnos
    $("#tableSupervisorsSample tbody").empty(); // Limpia la tabla de supervisores
    $(".nav-tabs .nav-link").removeClass("opened-tab").addClass("unopened-tab"); // Limpia las pestañas abiertas
    $(".nav-tabs .nav-link .check-icon").remove(); // Elimina los íconos de check
    areasSampleData = []; // Resetea los datos de áreas
    shiftsSampleData = []; // Resetea los datos de turnos
    supervisorsSampleData = []; // Resetea los datos de supervisores
    localStorage.removeItem("generalSampleFactor"); // Limpia el factor de muestra general
    localStorage.removeItem("sampleValue"); // Limpia el valor de muestra
    localStorage.removeItem("sampleFemaleValue"); // Limpia el valor de muestra de mujeres
    localStorage.removeItem("sampleMaleValue"); // Limpia el valor de muestra de hombres
    lastProjectIdForShifts = null; // Resetea el ID del último proyecto para turnos
    lastProjectIdForSupervisors = null; // Resetea el ID del último proyecto para supervisores
    saveSurveyShownOnSuccess = false; // Resetea el estado de la visualización del guardado de la encuesta
  }

  try {
    await fillMonthsSelect();
    await getProjects();
    await getServices();
  } catch (err) {
    Swal.fire("Error", err.message, "error");
    console.error(err);
  }
}

async function initAdminStatus() {
  const user = getCurrentUser();
  const userRegion = user.region;

  const getProjectStatusData = async () => {
    const url = apiUrl("status/getProjectStatus.php");
    const response = await authFetch(url, {
      method: "POST",
      body: { region: userRegion },
    });

    if (!response) return; // authFetch ya maneja logout y errores

    if (response.success && Array.isArray(response.data)) {
      return response.data; // Retorna los datos para DataTable
    } else {
      Swal.fire(
        "Atención",
        response.message || "No se encontraron proyectos programados.",
        "info"
      );
    }
  };

  const renderProjectStatusDataTable = async () => {
    const data = await getProjectStatusData();
    if (data.length === 0) return;

    const tableContainer = $("#status-table-container");
    const table = $(
      '<table class="table table-striped table-sm" id="project-status-table"></table>'
    );

    tableContainer.append(table);

    $("#project-status-table").DataTable({
      data: data,
      destroy: true, // necesario si vuelves a cargar la tabla
      columns: [
        { data: "ProjectName", title: "Cliente" },
        { data: "Headcount", title: "Headcount" },
        { data: "SampleValue", title: "Muestra" },
        { data: "MaleCount", title: "# Hombres" },
        { data: "SampleMaleValue", title: "M Hombres" },
        { data: "FemaleCount", title: "# Mujeres" },
        { data: "SampleFemaleValue", title: "M Mujeres" },
        {
          data: "SamplePercentage",
          title: "% Muestra",
          render: (data) => `${data}%`,
        },
        { data: "GuideID", title: "Guia" },
        { data: "ApplicationDate", title: "Fecha de Aplicación" },
      ],
      rowCallback: function (row, data) {
        if (data.SampleValue > 0) {
          $(row).addClass("sample-ok");
        } else {
          $(row).addClass("sample-missing");
        }

        // Agrega tooltip y click al primer <td>
        const firstCell = $("td", row).eq(0);

        firstCell
          .attr("title", "Ver detalles del proyecto") // Tooltip nativo
          .css("cursor", "pointer")
          .on("click", function () {
            // Aquí defines la navegación
            // window.location.href = `/tracker/ClientTracker?projectid=${data.ProjectID}`; // o usa otro dato
            goTo("admin/tracker/client");
            localStorage.setItem("selectedProjectStatus", data.ProjectID);
          });
      },
      order: [[2, "desc"]], // ← Columna 2 (índice empieza en 0), orden descendente
      // dom: "Bfrtip",
      // buttons: ["excelHtml5", "print"],
      language: {
        url: "https://cdn.datatables.net/plug-ins/2.3.2/i18n/es-MX.json",
        paginate: {
          previous: "◄",
          next: "►",
        },
      },
    });
  };

  try {
    await renderProjectStatusDataTable();
  } catch (err) {
    Swal.fire("Error", err.message, "error");
    console.error(err);
  }
}

async function initAdminClientTracker() {
  const user = getCurrentUser();
  const userRegion = user.region;
  let autoRefreshInterval = null;
  let lastProject = null;

  const getConfiguredProjects = async () => {
    const url = apiUrl("projects/getConfiguredProjects.php");

    const response = await authFetch(url, {
      method: "POST",
      body: { region: userRegion },
    });

    if (!response) return;

    if (response.success && Array.isArray(response.data)) {
      return response.data;
    } else {
      Swal.fire(
        "Atención",
        response.message || "No se encontraron proyectos activos.",
        "info"
      );
    }
  };

  const renderConfiguredProjects = async () => {
    const projects = await getConfiguredProjects();
    if (!projects || projects.length === 0) return;

    const selectProjects = $("#client-configured-select");
    const selectYears = $("#years-select");

    selectProjects.empty();
    selectYears.empty();

    selectProjects.append(new Option("", "", true, true));
    selectYears.append(new Option("", "", true, true));

    const uniquePeriods = new Set();
    const currentYear = new Date().getFullYear().toString();
    let selectedIndex = 0;

    projects.forEach((project) => {
      selectProjects.append(
        new Option(project.ProjectName, project.ProjectID.trim())
      );

      if (!uniquePeriods.has(project.Period)) {
        uniquePeriods.add(project.Period);

        const isCurrent = project.Period === currentYear;
        const option = new Option(
          project.Period,
          project.Period,
          isCurrent,
          isCurrent
        );
        selectYears.append(option);
      }
    });
  };

  const areaTrackerColumns = [
    { key: "AreaName" },
    { key: "TotalEmployees" },
    { key: "SampleCompleted", realtime: true },
    { key: "MaleEmployees" },
    { key: "MaleCompleted", realtime: true },
    { key: "FemaleEmployees" },
    { key: "FemaleCompleted", realtime: true },
    { key: "Completed" },
  ];

  const supervisorTrackerColumns = [
    { key: "SupervisorName" },
    { key: "TotalEmployees" },
    { key: "SampleCompleted", realtime: true },
    { key: "MaleEmployees" },
    { key: "MaleCompleted", realtime: true },
    { key: "FemaleEmployees" },
    { key: "FemaleCompleted", realtime: true },
    { key: "Completed" },
  ];

  const shiftTrackerColumns = [
    { key: "ShiftName" },
    { key: "TotalEmployees" },
    { key: "SampleCompleted", realtime: true },
    { key: "MaleEmployees" },
    { key: "MaleCompleted", realtime: true },
    { key: "FemaleEmployees" },
    { key: "FemaleCompleted", realtime: true },
    { key: "Completed" },
  ];

  $(document).on("change", "#client-configured-select", async function () {
    const projectId = $(this).val();
    const selectYears = $("#years-select").val();
    if (!projectId || !selectYears) return;

    const data = await fetchTableData("getGeneralData.php", {
      projectId,
      selectYears,
    });

    if (data) renderTableGeneralTracker(data);
    else
      return swal.fire(
        "Error",
        "No se encontraron datos para el seguimiento general.",
        "error"
      );

    const areaData = await fetchTableData("getAreaData.php", {
      projectId,
      selectYears,
    });

    if (areaData)
      renderTrackerTables(
        "#table-area-client-tracker",
        areaData,
        areaTrackerColumns
      );
    else
      return swal.fire(
        "Error",
        "No se encontraron datos para el seguimiento por área.",
        "error"
      );

    const supervisorData = await fetchTableData("getSupervisorData.php", {
      projectId,
      selectYears,
    });

    if (supervisorData)
      renderTrackerTables(
        "#table-supervisor-client-tracker",
        supervisorData,
        supervisorTrackerColumns
      );

    const shiftData = await fetchTableData("getShiftData.php", {
      projectId,
      selectYears,
    });
    if (shiftData)
      renderTrackerTables(
        "#table-workshift-client-tracker",
        shiftData,
        shiftTrackerColumns
      );
    else
      return swal.fire(
        "Error",
        "No se encontraron datos para el seguimiento por turno.",
        "error"
      );
  });

  async function fetchTableData(endpoint, payload) {
    const url = apiUrl("tracker/" + endpoint);
    const response = await authFetch(url, {
      method: "POST",
      body: payload,
    });
    if (response?.success && Array.isArray(response.data)) return response.data;
    return null;
  }

  const renderTableGeneralTracker = (data) => {
    const tableBody = $("#table-general-client-tracker tbody");
    tableBody.empty();

    const item = data[0]; // Solo una fila para proyecto general
    const row = $("<tr></tr>");

    // Render con placeholders en columnas vacías
    row.append($("<td></td>").text(item.SampleValue)); // Col 0
    row.append($("<td></td>").attr("data-col", "SampleCompleted")); // Col 1 (realtime)
    row.append($("<td></td>").text(item.GeneralSampleFactor * 100 + "%")); // Col 2
    row.append($("<td></td>").text(item.SampleMaleValue)); // Col 3
    row.append($("<td></td>").attr("data-col", "MaleCompleted")); // Col 4 (realtime)
    row.append($("<td></td>").text(item.SampleFemaleValue)); // Col 5
    row.append($("<td></td>").attr("data-col", "FemaleCompleted")); // Col 6 (realtime)

    tableBody.append(row);
  };

  // const renderTableAreaTracker = (data) => {
  //   const id = "#table-area-client-tracker";
  //   // Verifica si la tabla ya existe y destrúyela si es necesario
  //   if ($.fn.DataTable.isDataTable(id)) {
  //     $(id).DataTable().clear().destroy();
  //   }

  //   const tableBody = $(id + " tbody");
  //   tableBody.empty();

  //   data.forEach((element) => {
  //     const row = $("<tr></tr>");
  //     row.append($("<td></td>").text(element.AreaName));
  //     row.append($("<td></td>").text(element.TotalEmployees));
  //     row.append($("<td></td>").attr("data-col", "SampleCompleted")); //(realtime)
  //     row.append($("<td></td>").text(element.MaleEmployees));
  //     row.append($("<td></td>").attr("data-col", "MaleCompleted")); //(realtime)
  //     row.append($("<td></td>").text(element.FemaleEmployees));
  //     row.append($("<td></td>").attr("data-col", "FemaleCompleted")); //(realtime)

  //     tableBody.append(row);
  //   });

  //   // Inicializar DataTable
  //   $(id).DataTable({
  //     paging: false,
  //     searching: false,
  //     info: false,
  //     ordering: false,
  //     scrollY: "300px",
  //     scrollCollapse: true,
  //   });
  // };

  // const renderTableSupervisorTracker = (data) => {
  //   const tableBody = $("#table-supervisor-client-tracker tbody");
  //   tableBody.empty();

  //   data.forEach((element) => {
  //     const row = $("<tr></tr>");
  //     row.append($("<td></td>").text(element.SupervisorName));
  //     row.append($("<td></td>").text(element.TotalEmployees));
  //     row.append($("<td></td>").attr("data-col", "SampleCompleted"));
  //     row.append($("<td></td>").text(element.MaleEmployees));
  //     row.append($("<td></td>").attr("data-col", "MaleCompleted"));
  //     row.append($("<td></td>").text(element.FemaleEmployees));
  //     row.append($("<td></td>").attr("data-col", "FemaleCompleted"));

  //     tableBody.append(row);
  //   });
  // };

  // const renderTableShiftTracker = (data) => {
  //   const tableBody = $("#table-workshift-client-tracker tbody");
  //   tableBody.empty();

  //   data.forEach((element) => {
  //     const row = $("<tr></tr>");
  //     row.append($("<td></td>").text(element.ShiftName));
  //     row.append($("<td></td>").text(element.TotalEmployees));
  //     row.append($("<td></td>").attr("data-col", "SampleCompleted"));
  //     row.append($("<td></td>").text(element.MaleEmployees));
  //     row.append($("<td></td>").attr("data-col", "MaleCompleted"));
  //     row.append($("<td></td>").text(element.FemaleEmployees));
  //     row.append($("<td></td>").attr("data-col", "FemaleCompleted"));

  //     tableBody.append(row);
  //   });
  // };

  function renderTrackerTables(tableId, data, columns) {
    // Verifica y destruye DataTable previa
    if ($.fn.dataTable && $.fn.dataTable.isDataTable(tableId)) {
      $(tableId).DataTable().clear().destroy();
    }

    const tableBody = $(tableId + " tbody");
    tableBody.empty();

    data.forEach((element) => {
      const row = $("<tr></tr>");

      columns.forEach((col) => {
        if (col.realtime) {
          row.append($("<td></td>").attr("data-col", col.key));
        } else {
          row.append($("<td></td>").text(element[col.key] ?? ""));
        }
      });

      tableBody.append(row);
    });

    // Inicializar DataTable
    $(tableId).DataTable({
      paging: false,
      searching: true,
      info: false,
      ordering: false,
      scrollY: "300px",
      scrollCollapse: true,
    });
  }

  $(document).on("click", "#btn-refresh-data", async function () {
    const project = $("#client-configured-select option:selected")
      .text()
      .trim();
    const year = $("#years-select").val();
    if (!project || !year) return;

    $(this).html(
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Actualizando...'
    );

    await refreshRealTimeTables(project, year);

    $(this).html('<i class="bi bi-arrow-clockwise"></i> Actualizar');
    // Si no hay intervalo activo o el proyecto cambió
    if (!autoRefreshInterval || project !== lastProject) {
      if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
      }

      lastProject = project;

      // Iniciar intervalo automático solo para este proyecto
      autoRefreshInterval = setInterval(async () => {
        const currentProject = $("#client-configured-select option:selected")
          .text()
          .trim();
        const currentYear = $("#years-select").val();

        // Si el proyecto cambió, detener intervalo
        if (currentProject !== lastProject) {
          clearInterval(autoRefreshInterval);
          autoRefreshInterval = null;
          return;
        }

        try {
          await refreshRealTimeTables(currentProject, currentYear);
        } catch (error) {
          console.error("Error al actualizar tablas en tiempo real:", error);
        }
      }, 10000); // cada 10 segundos
    }
  });

  async function refreshRealTimeTables(project, year) {
    const general = await fetchTableData("realTimeData.php", {
      type: "general",
      project,
      selectYears: year,
    });
    if (general) updateGeneralTableRealTime(general);

    const realTimeArea = await fetchTableData("realTimeData.php", {
      type: "area",
      project,
      selectYears: year,
    });

    if (realTimeArea)
      updateDynamicTablesRealTime(
        "#table-area-client-tracker",
        realTimeArea,
        areaTrackerColumns,
        "AreaName"
      );

    const realTimeSupervisor = await fetchTableData("realTimeData.php", {
      type: "supervisor",
      project,
      selectYears: year,
    });

    if (realTimeSupervisor)
      updateDynamicTablesRealTime(
        "#table-supervisor-client-tracker",
        realTimeSupervisor,
        supervisorTrackerColumns,
        "SupervisorName"
      );

    const realTimeShift = await fetchTableData("realTimeData.php", {
      type: "shift",
      project,
      selectYears: year,
    });

    if (realTimeShift)
      updateDynamicTablesRealTime(
        "#table-workshift-client-tracker",
        realTimeShift,
        shiftTrackerColumns,
        "ShiftName"
      );
  }

  const updateGeneralTableRealTime = (realtimeData) => {
    const row = $("#table-general-client-tracker tbody tr").first();
    if (!row.length) return;

    const data = realtimeData[0]; // Solo una fila esperada

    row.find("td[data-col='SampleCompleted']").text(data.Completed);
    row.find("td[data-col='MaleCompleted']").text(data.MaleCount);
    row.find("td[data-col='FemaleCompleted']").text(data.FemaleCount);
  };

  function updateDynamicTablesRealTime(
    tableId,
    realtimeData,
    columns,
    matchKey
  ) {
    const rows = $(tableId + " tbody tr");

    rows.each(function () {
      const rowKeyValue = $(this).find("td").first().text().trim();

      const data = realtimeData.find(
        (item) => String(item[matchKey]).trim() === rowKeyValue
      );

      if (!data) return;

      // Actualizar solo las columnas marcadas como realtime
      columns.forEach((col) => {
        if (col.realtime) {
          const $cell = $(this).find(`td[data-col='${col.key}']`);
          const newValue = data[col.key] ?? "";

          if ($cell.text() !== String(newValue)) {
            $cell.text(newValue).addClass("cell-updated");

            setTimeout(() => $cell.removeClass("cell-updated"), 1000);
          }
        }
      });
    });
  }

  try {
    await renderConfiguredProjects();

    const projectId = (
      localStorage.getItem("selectedProjectStatus") || ""
    ).trim();
    $("#client-configured-select").val(projectId).trigger("change");

    setInterval(async () => {
      localStorage.removeItem("selectedProjectStatus");
    }, 5000);

  } catch (error) {
    console.error("Error al renderizar proyectos configurados:", error);
  }
}
