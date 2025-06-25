// Refactor completo de forms.js con mejoras de estructura, claridad y seguridad
async function initForms() {
  const dataEmployee = JSON.parse(sessionStorage.getItem("dataEmployee"));
  const cbCodigo = dataEmployee.CB_CODIGO;
  const projectId = dataEmployee.projectId ?? "";
  const client = dataEmployee.cliente ?? "";

  $("#clientName").text(client);

  if (!projectId) {
    Swal.fire(
      "Aviso",
      "No se encontro el proyecto para el colaborador",
      "warning"
    );
    return;
  }

  if (!dataEmployee) {
    Swal.fire({
      title: "Error",
      text: "No se encontraron datos del colaborador. Por favor, verifica la información del paso anterior.",
      icon: "error",
      showCancelButton: true,
      confirmButtonText: "Verificar información",
      cancelButtonText: "Cancelar",
      allowOutsideClick: false,
      footer: "<small>Si el problema persiste, contacta al aplicador.</small>",
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = "verifyEmployee"; // Cambia esta URL según necesites
      }
    });

    return;
  }

  const currentYear = new Date().getFullYear();
  let guideType = null;
  let questionsGuide = [];
  let complementaryQuestions = [];
  let answers = [];
  let currentIndex = 0;
  let renderedIndexes = [];
  let currentSet = "main";

  const expectedAnswersViolenciaLaboral = {
    2: {
      105: "Siempre",
      106: "Nunca",
      107: "Nunca",
      108: "Nunca",
      109: "Nunca",
      110: "Nunca",
      111: "Nunca",
      112: "Nunca",
    },
    3: {
      57: "Siempre",
      58: "Nunca",
      59: "Nunca",
      60: "Nunca",
      61: "Nunca",
      62: "Nunca",
      63: "Nunca",
      64: "Nunca",
    },
  };

  const guideConditionalJumps = {
    2: [
      {
        triggerQuestionID: 113,
        jumpToID: 116,
        message: "¿En tu trabajo brindas servicio a clientes o usuarios?",
        type: "skip",
      },
      {
        triggerQuestionID: 116,
        message: "¿Realizas funciones de supervisión?",
        type: "supplementary",
      },
    ],
    3: [
      {
        triggerQuestionID: 65,
        jumpToID: 69,
        message: "¿En tu trabajo brindas servicio a clientes o usuarios?",
        type: "skip",
      },
      {
        triggerQuestionID: 69,
        message: "¿Realizas funciones de supervisión?",
        type: "supplementary",
      },
    ],
  };

  const getCurrentQuestions = () =>
    currentSet === "main" ? questionsGuide : complementaryQuestions;
  const getCurrentQuestion = () => getCurrentQuestions()[currentIndex];

  const fetchSurveyConfig = async () => {
    const response = await $.ajax({
      url: apiUrl("guides/getSurveyConfig.php"),
      method: "POST",
      contentType: "application/json",
      dataType: "json",
      data: JSON.stringify({ project_id: projectId }),
    });
    if (!response.success) throw new Error(response.message);
    guideType = response.data.GuideID;
  };

  const fetchGuideInfo = async () => {
    const res = await $.ajax({
      url: apiUrl("guides/getInfoGuide.php"),
      method: "POST",
      contentType: "application/json",
      dataType: "json",
      data: JSON.stringify({ guide_type: guideType }),
    });
    if (!res.success) throw new Error(res.message);
    $("#titleGuide .float-end").append(
      `<span class="ms-2">${res.data.GuideName}</span>`
    );
    $("#descriptionGuide").append(
      `<p class="mb-0 mt-3">${res.data.Description}</p>`
    );
  };

  const loadInstructions = async () => {
    await loadForm("guides/loadInstructions.php", "#div-instructions");
  };

  const fetchGuideData = async () => {
    const response = await $.ajax({
      url: apiUrl("guides/guideData.php"),
      method: "POST",
      contentType: "application/json",
      dataType: "json",
      data: JSON.stringify({ guideValue: guideType }),
    });
    if (!response.success || !Array.isArray(response.data))
      throw new Error("No se pudieron cargar las preguntas");
    questionsGuide = response.data;
  };

  const fetchSupplementaryQuestions = async () => {
    const response = await $.ajax({
      url: apiUrl("guides/supplementaryQuestionsData.php"),
      method: "POST",
      contentType: "application/json",
      dataType: "json",
      data: JSON.stringify({ project_id: projectId }),
    });

    if (response.success && Array.isArray(response.data)) {
      complementaryQuestions = response.data;
      currentSet = "complementary";
      currentIndex = 0;
      $("#titleGuide span span").empty();
      $("#titleGuide .float-end").append(
        `<span class="ms-2">Complementarias y Servicios TECMA</span>`
      );
      renderQuestion(currentIndex);
    } else {
      Swal.fire(
        "Atención",
        "No se encontraron preguntas complementarias y de servicios. Notifique al aplicador.",
        "info"
      );
    }
  };

  const handleConditionalJumps = async (questionID) => {
    const jumps = guideConditionalJumps[guideType] || [];
    const jumpConfig = jumps.find((j) => j.triggerQuestionID === questionID);
    if (!jumpConfig) return false;

    const result = await Swal.fire({
      title: "Pregunta especial",
      text: jumpConfig.message,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Sí",
      cancelButtonText: "No",
      allowOutsideClick: false,
    });

    if (!result.isConfirmed) {
      if (jumpConfig.type === "skip") {
        const preguntas = getCurrentQuestions();
        const targetIndex = preguntas.findIndex(
          (q) => Number(q.QuestionID) === jumpConfig.jumpToID
        );
        if (targetIndex !== -1) {
          currentIndex = targetIndex;
          await renderQuestion(currentIndex);
          return true;
        }
      } else if (jumpConfig.type === "supplementary") {
        await fetchSupplementaryQuestions();
        return true;
      }
    }

    return false;
  };

  const renderQuestion = async (index) => {
    const q = getCurrentQuestion();
    if (!q) return;
    const questionID = Number(q.QuestionID);

    const jumped = await handleConditionalJumps(questionID);
    if (jumped) return;

    const html = `
      <div class="question-box mt-5 table-responsive">
        <table class="table table-borderless align-middle text-center">
          <thead>
            <tr>
              <th colspan="2" class="text-start">
                <span>${
                  currentSet === "main" ? q.DimensionName : q.CategoryName
                }</span>
              </th>
              ${q.Scale.map((op) => `<th>${op.option}</th>`).join("")}
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="fw-bold"><span>${index + 1}</span></td>
              <td class="text-start">${q.QuestionText}</td>
              ${q.Scale.map(
                (op) => `
                <td>
                  <label class="d-block w-100 h-100 m-0 p-3">
                    <input type="radio" name="answer_${q.QuestionID}" value="${op.value}" class="me-2">
                  </label>
                </td>`
              ).join("")}
            </tr>
          </tbody>
        </table>
      </div>`;

    $("#question-container").html(html);

    const existing = answers.find(
      (a) => a.QuestionID === questionID && a.set === currentSet
    );
    if (existing) {
      setTimeout(() => {
        $(
          `input[name="answer_${q.QuestionID}"][value="${existing.Answer}"]`
        ).prop("checked", true);
      }, 0);
    }

    if (!renderedIndexes.includes(index)) renderedIndexes.push(index);
    $("#prevQuestionBtn").toggleClass("d-none", currentIndex === 0);
  };

  const submitEmployeeData = async () => {
    const response = await $.ajax({
      url: apiUrl("employees/submitEmployeeData.php"),
      method: "POST",
      contentType: "application/json",
      dataType: "json",
      data: JSON.stringify(dataEmployee),
    });

    if (!response || !response.success) {
      throw new Error("Error al guardar los datos del empleado.");
    }
  };

  const submitSurveyAnswers = async () => {
    const payload = {
      employeeNumber: cbCodigo,
      answers: answers,
    };

    const response = await $.ajax({
      url: apiUrl("answers/submitAnswers.php"),
      method: "POST",
      contentType: "application/json",
      dataType: "json",
      data: JSON.stringify(payload),
    });

    if (!response || !response.success) {
      throw new Error("Error al guardar las respuestas de la encuesta.");
    }
  };

  const submitAll = async () => {
    try {
      $("#submitSurveyBtn").prop("disabled", true);

      await submitEmployeeData(); // 1️⃣ Guardar datos del colaborador
      await submitSurveyAnswers(); // 2️⃣ Guardar respuestas
    } catch (error) {
      Swal.fire("Error", error.message, "error");
      throw error; // Re-lanza para que el catch externo lo detecte también si lo deseas
    } finally {
      $("#submitSurveyBtn").prop("disabled", false);
    }
  };

  $(document).on("click", "#btnContinue", () => {
    $("#div-instructions").addClass("d-none");
    $("#div-indications, #div-forms").removeClass("d-none");
    renderQuestion(currentIndex);
  });

  $("#nextQuestionBtn").on("click", async () => {
    const q = getCurrentQuestion();
    const selectedValue = $(
      `input[name="answer_${q.QuestionID}"]:checked`
    ).val();

    if (!selectedValue) {
      Swal.fire({
        toast: true,
        position: "bottom",
        icon: "warning",
        title: "Por favor selecciona una opción",
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

    const questionID = Number(q.QuestionID);
    const numericValue = Number(selectedValue);
    const expected = expectedAnswersViolenciaLaboral[guideType]?.[questionID];

    const existing = answers.find(
      (a) => a.QuestionID === questionID && a.set === currentSet
    );
    if (existing) {
      existing.Answer = numericValue;
    } else {
      answers.push({
        QuestionID: questionID,
        Answer: numericValue,
        set: currentSet,
      });
    }

    if (q.DimensionName === "Violencia laboral" && expected) {
      const selectedLabel = q.Scale.find(
        (s) => s.value === numericValue
      )?.option;
      if (selectedLabel !== expected) {
        const confirm = await Swal.fire({
          title: "Confirmar respuesta",
          text: "Tu respuesta indica posible violencia laboral. Si es correcto selecciona Acepto.",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Acepto",
          cancelButtonText: "Cambiar respuesta",
        });
        if (!confirm.isConfirmed) return;
      }
    }

    currentIndex++;
    if (currentIndex < getCurrentQuestions().length) {
      renderQuestion(currentIndex);
    } else if (currentSet === "main") {
      await fetchSupplementaryQuestions();
    } else if (currentSet === "complementary") {
      showFinalView();
    }
  });

  $("#prevQuestionBtn").on("click", () => {
    if (renderedIndexes.length > 1) {
      renderedIndexes.pop();
      currentIndex = renderedIndexes[renderedIndexes.length - 1];
      renderQuestion(currentIndex);
    }
  });

  function showFinalView() {
    const $container = $("#question-container");
    $("#indicationsText").addClass("d-none");
    $("#nextQuestionBtn").addClass("d-none");
    $("#prevQuestionBtn").addClass("d-none");
    $container.empty(); // Limpia el contenedor

    const html = `
    <div class="text-center mt-5">
      <h3>Has terminado de responder el cuestionario.</h3>
      <p>Haz clic en el botón para enviar tus respuestas.</p>
      <button id="submitSurveyBtn" class="btn btn-primary mt-3">Enviar cuestionario</button>
    </div>
  `;

    $container.append(html);

    $("#submitSurveyBtn").on("click", async function () {
      Swal.fire({
        title: "Guardando...",
        text: "Espere un momento, la página se redireccionará automáticamente al finalizar.",
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      try {
        await submitAll();

        sessionStorage.clear();

        Swal.fire({
          icon: "success",
          title: "¡Gracias!",
          text: "Tus respuestas han sido enviadas correctamente.",
          timer: 3000,
          timerProgressBar: true,
          showConfirmButton: false,
        });

        // Redirigir después de un pequeño delay
        setTimeout(() => {
          window.location.href = `verifyEmployee`;
        }, 3000);
      } catch (error) {
        // Ya manejas errores en `submitAll`, pero por si acaso:
        console.error("Error al guardar:", error);
        // Swal.fire de error ya se lanza desde `submitAll`, así que no es obligatorio repetirlo aquí
      }
    });
  }

  try {
    await loadInstructions();
    await fetchSurveyConfig();
    await fetchGuideInfo();
    await fetchGuideData();
    //await fetchSupplementaryQuestions();
  } catch (err) {
    Swal.fire("Error", err.message, "error");
    console.error(err);
  }
}
