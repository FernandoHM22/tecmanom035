async function initForms() {
  // En página de encuesta
  const dataEmployee = JSON.parse(sessionStorage.getItem("dataEmployee"));
  console.log(dataEmployee);
  const paramsURL = new URLSearchParams(window.location.search);
  const projectId = paramsURL.get("project"); // cambia ?project=AMCOR en la URL
  $("#clientName").text(projectId);
  if (!projectId) {
    Swal.fire("Error", "Falta el parámetro ?project en la URL", "error");
    return;
  }

  let guideType = null;
  const currentYear = new Date().getFullYear();

  const validReponseLaboralViolence = {
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
    2: {
      alert1: {
        triggerQuestionID: 113,
        jumpToID: 116,
        message: "¿En tu trabajo brindas servicio a clientes o usuarios?",
      },
      alert2: {
        triggerQuestionID: 116,
        jumpTo: "complementary",
        message: "¿Realizas funciones de supervisión?",
      },
    },
    3: {
      alert1: {
        triggerQuestionID: 65,
        jumpToID: 69,
        message: "¿En tu trabajo brindas servicio a clientes o usuarios?",
      },
      alert2: {
        triggerQuestionID: 69,
        jumpTo: "complementary",
        message: "¿Realizas funciones de supervisión?",
      },
    },
  };

  let questionsGuide = [];
  let complementaryQuestions = [];
  let answers = [];
  let currentIndex = 0;
  let renderedIndexes = [];
  let currentSet = "main";

  const getCurrentQuestions = () =>
    currentSet === "main" ? questionsGuide : complementaryQuestions;

  const getCurrentQuestion = () => getCurrentQuestions()[currentIndex];

  // 1. Obtener el tipo de guía desde SurveyConfigs
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

  // 2. Obtener el GuideID desde tabla Guides
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
      ` <span class="ms-2">${res.data.GuideName}</span>`
    );
    $("#descriptionGuide").append(
      ` <p class="mb-0 mt-3">${res.data.Description}</p>`
    );
  };

  // 3. Cargar instrucciones
  const loadInstructions = async () => {
    await loadForm("guides/loadInstructions.php", "#div-instructions");
  };

  // 4. Obtener preguntas principales
  const fetchGuideData = async () => {
    const response = await $.ajax({
      url: apiUrl("guides/guideData.php"),
      method: "POST",
      contentType: "application/json",
      dataType: "json",
      data: JSON.stringify({ guideValue: guideType }),
    });

    if (!response.success || !Array.isArray(response.data)) {
      throw new Error("No se pudieron cargar las preguntas");
    }

    // questionsGuide = response.data.filter((q) =>
    //   [
    //     "violencia laboral",
    //     "cargas psicológicas emocionales",
    //     "deficiente relación con los colaboradores que supervisa",
    //   ].includes(q.DimensionName?.toLowerCase())
    // );

    questionsGuide = response.data;
  };

  // 5. Obtener preguntas complementarias filtradas por servicios
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
        ` <span class="ms-2">Complementarias y Servicios TECMA</span>`
      );
      renderQuestion(currentIndex);
    } else {
      Swal.fire(
        "Atención",
        "No se encontraron preguntas complementarias.",
        "info"
      );
    }
  };

  // 6. Renderizar pregunta
  const renderQuestion = async (index) => {
    const q = getCurrentQuestion();
    if (!q) return;
    QuestionID = Number(q.QuestionID);
    guideType = Number(guideType);

    if (
      (guideType === 2 && (QuestionID === 113 || QuestionID === 116)) ||
      (guideType === 3 && (QuestionID === 65 || QuestionID === 69))
    ) {
      const jumped = await handleConditionalJumps(QuestionID, guideType);
      console.log("Saltó pregunta:", jumped);
      if (jumped) return; // Si hubo salto, detenemos avance normal
    }

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
                </td>
              `
             ).join("")}
            </tr>
          </tbody>
        </table>
      </div>
    `;

    $("#question-container").html(html);

    const respuestaObj = answers.find(
      (a) => Number(a.QuestionID) === Number(q.QuestionID)
    );
    if (respuestaObj) {
      setTimeout(() => {
        $(
          `input[name="answer_${q.QuestionID}"][value="${respuestaObj.Answer}"]`
        ).prop("checked", true);
      }, 0);
    }

    if (renderedIndexes[renderedIndexes.length - 1] !== index) {
      renderedIndexes.push(index);
    }

    if (currentIndex > 0) {
      $("#prevQuestionBtn").removeClass("d-none");
    } else {
      $("#prevQuestionBtn").addClass("d-none");
    }
  };

  // 6.1 Manejo de saltos condicionales
  async function handleConditionalJumps(QuestionID, guideType) {
    // Primera alerta (clientes o usuarios)
    console.log(QuestionID, guideType);
    if (
      (guideType === 3 && QuestionID === 65) ||
      (guideType === 2 && QuestionID === 113)
    ) {
      console.log("Pregunta especial activada");
      const alerta1 = await Swal.fire({
        title: "Pregunta especial",
        text: "¿En tu trabajo brindas servicio a clientes o usuarios?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí",
        cancelButtonText: "No",
        allowOutsideClick: false,
      });

      if (!alerta1.isConfirmed) {
        const preguntas = getCurrentQuestions();
        console.log("Preguntas:", preguntas);
        const nextId = guideType === 3 ? 69 : 116;
        console.log(nextId);

        const targetIndex = preguntas.findIndex(
          (q) => Number(q.QuestionID) === nextId
        );

        if (targetIndex !== -1) {
          currentIndex = targetIndex;
          renderQuestion(currentIndex);
          return true;
        }
      }
    }

    // Segunda alerta (supervisión)
    if (
      (guideType === 3 && QuestionID === 69) ||
      (guideType === 2 && QuestionID === 116)
    ) {
      const alerta2 = await Swal.fire({
        title: "Pregunta especial",
        text: "¿Realizas funciones de supervisión?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí",
        cancelButtonText: "No",
        allowOutsideClick: false,
      });

      if (!alerta2.isConfirmed) {
        await fetchSupplementaryQuestions();
        return true;
      }
    }

    return false; // No hubo salto
  }

  // 7. Eventos
  $(document).on("click", "#btnContinue", () => {
    $("#div-instructions").addClass("d-none");
    $("#div-indications").removeClass("d-none");
    $("#div-forms").removeClass("d-none");
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

    const qid = Number(q.QuestionID);
    const numericValue = Number(selectedValue); // <-- porque el value es numérico
    const expectedResponse = validReponseLaboralViolence[guideType]?.[qid];

    // Actualiza o agrega respuesta
    const existingIndex = answers.findIndex((a) => a.QuestionID === qid);
    if (existingIndex !== -1) {
      answers[existingIndex].Answer = numericValue;
    } else {
      answers.push({ QuestionID: qid, Answer: numericValue });
    }

    // Validación especial para preguntas de violencia laboral
    if (
      q.DimensionName === "Violencia laboral" &&
      expectedResponse &&
      q.Scale.find((s) => s.value === numericValue)?.option !== expectedResponse
    ) {
      const confirm = await Swal.fire({
        title: "Confirmar respuesta",
        text: "Tu respuesta anterior indica que estás pasando por una situación de violencia laboral y se procederá a la investigación del caso, si esto es correcto selecciona Acepto, de lo contrario cambia tu respuesta.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Acepto",
        cancelButtonText: "Cambiar respuesta",
      });

      if (!confirm.isConfirmed) return;
    }

    // if (guideType === "3" && qid === 64) {
    //   const alerta1 = await Swal.fire({
    //     title: "Pregunta especial",
    //     text: "¿En tu trabajo brindas servicio a clientes o usuarios?",
    //     icon: "question",
    //     showCancelButton: true,
    //     confirmButtonText: "Sí",
    //     cancelButtonText: "No",
    //     allowOutsideClick: false
    //   });

    //   if (!alerta1.isConfirmed) {
    //     const preguntas = getCurrentQuestions();
    //     console.log(qid)
    //     console.log("Preguntas:", preguntas);
    //     const targetIndex = preguntas.findIndex((q) => q.QuestionID === 68);
    //     console.log("Target index for 68:", targetIndex);

    //     if (targetIndex !== -1) {
    //       currentIndex = targetIndex;
    //       renderQuestion(currentIndex);
    //       return; // Detiene el avance normal
    //     }
    //   }
    // }

    // if (guideType === '3' && qid === 68) {
    //   const alerta2 = await Swal.fire({
    //     title: "Pregunta especial",
    //     text: "¿Realizas funciones de supervisión?",
    //     icon: "question",
    //     showCancelButton: true,
    //     confirmButtonText: "Sí",
    //     cancelButtonText: "No",
    //     allowOutsideClick:false
    //   });

    //   if (!alerta2.isConfirmed) {
    //     // Saltar directamente a complementarias
    //     if (currentSet === "main") {
    //       await fetchSupplementaryQuestions();
    //       return; // Detener flujo normal
    //     }
    //   }
    // }

    currentIndex++;
    if (currentIndex < getCurrentQuestions().length) {
      renderQuestion(currentIndex);
    } else if (currentSet === "main") {
      await fetchSupplementaryQuestions();
    } else {
      console.log("Finalizado", answers);
      alert("¡Has completado el cuestionario!");
      // aquí puedes enviar por AJAX
    }
  });

  $("#prevQuestionBtn").on("click", () => {
    if (renderedIndexes.length > 1) {
      // Eliminar el índice actual
      renderedIndexes.pop();
      // Obtener el anterior
      currentIndex = renderedIndexes[renderedIndexes.length - 1];
      renderQuestion(currentIndex);
    }
  });

  // 8. Inicialización
  try {
    await loadInstructions();
    await fetchSurveyConfig();
    await fetchGuideInfo();
    await fetchGuideData();
  } catch (err) {
    Swal.fire("Error", err.message, "error");
    console.error(err);
  }
}
