const ModalHandlers = {
  // contenido para modal de muestras
  sample(modal, projectElement) {
    const headcount = parseInt($(projectElement).attr("data-headcount"), 10);
    const countFemale = parseInt(
      $(projectElement).attr("data-female-count"),
      10
    );
    const countMale = parseInt($(projectElement).attr("data-male-count"), 10);

    const alertHeadcount = headcount < 60 ? "alert alert-info" : "d-none";

    const content = `
      <p>La selección de muestra se basa en la siguiente ecuación:</p>
      <div class="text-center mb-3 d-block mx-auto">
        <img src="../public/img/sample-equation.png" alt="Ecuación de selección de muestra" class="img-fluid" width="50%">
      </div>
      <div class="text-start">
        <p class="m-0">En donde:</p>
        <ul class="text-start">
          <li><strong>N</strong> es el número total de colaboradores</li>
          <li><strong>n</strong> es el número de colaboradores a los que se les aplicará los cuestionarios</li>
          <li><strong>0.9604 y 0.0025</strong> son los valores constantes en la fórmula de selección de muestra</li>
        </ul>
      </div>
    <hr>
    <div class="${alertHeadcount} mb-3" role="alert">
        <p class="mb-0">El número total de colaboradores debe ser mayor o igual a 60 colaboradores para calcular la muestra. Caso contrario se tomara el 100%</p>
    </div>
    <div class="row">
        <div class="col">
            <div class="form-group row">
                <label for="input_headcount" class="col-sm-6 col-form-label">Headcount total</label>
                <div class="col-sm-6">
                    <input type="number" class="form-control form-control-sm" disabled id="input_headcount" value="${headcount}">
                </div>
            </div>
        </div>
        <div class="col">
            <div class="form-group row">
                <label for="input_female" class="col-sm-6 col-form-label"># Mujeres</label>
                <div class="col-sm-6">
                    <input type="number" class="form-control form-control-sm" disabled id="input_female" value="${countFemale}">
                </div>
            </div>
        </div>
             <div class="col">
            <div class="form-group row">
                <label for="input_sample_female" class="col-sm-6 col-form-label">M de Mujeres</label>
                <div class="col-sm-6">
                    <input type="number" class="form-control form-control-sm" disabled id="input_sample_female">
                </div>
            </div>
        </div>
    </div>
        <div class="row">
        <div class="col">
            <div class="form-group row">
                <label for="input_sample" class="col-sm-6 col-form-label">Muestra requerida</label>
                <div class="col-sm-6">
                    <input type="number" class="form-control form-control-sm" disabled id="input_sample">
                </div>
            </div>
        </div>
        <div class="col">
            <div class="form-group row">
                <label for="input_male" class="col-sm-6 col-form-label"># Hombres</label>
                <div class="col-sm-6">
                    <input type="number" class="form-control form-control-sm" disabled id="input_male" value="${countMale}">
                </div>
            </div>
        </div>
             <div class="col">
            <div class="form-group row">
                <label for="input_sample_male" class="col-sm-6 col-form-label">M de Hombres</label>
                <div class="col-sm-6">
                    <input type="number" class="form-control form-control-sm" disabled id="input_sample_male">
                </div>
            </div>
        </div>
    </div>
    `;
    $(modal).find(".modal-body").html(content);
  },
  changeLogo(modal, projectElement) {
    const content = `
        <div id="upload-new-logo" class="mt-3">
            <input class="form-control mb-2" type="file" id="new-logo" accept="image/png">
            <button id="confirm-new-logo" class="btn btn-primary">Confirmar carga</button>
        </div>
        `;

    $(modal).find(".modal-body").html(content);
  },

  //   <div class="text-center mt-5">
  //   <button class="btn btn-primary btn-sm" id="btnCalculateSample">Calcular muestra</button>
  // </div>

  //   userEdit(modal) {
  //     const content = `
  //       <p>Editando usuario:</p>
  //       <input type="email" class="form-control form-control-sm" placeholder="Correo electrónico">
  //     `;
  //     $(modal).find(".modal-body").html(content);
  //   },

  //   confirmDelete(modal) {
  //     const content = `
  //       <p>¿Estás seguro que deseas eliminar este registro?</p>
  //       <button class="btn btn-danger">Confirmar</button>
  //     `;
  //     $(modal).find(".modal-body").html(content);
  //   },
};
