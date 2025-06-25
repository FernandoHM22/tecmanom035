<div id="containerValidateEmployee">
    <div class="row mt-3 mb-4">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-white rounded-end shadow-sm">
                    <img src="<?= asset('public/img/icons/icon-location.svg') ?>" class="icon" alt="">
                    REGIÓN
                </span>
                <select class="form-control form-control-sm" name="select_region" id="select_region" required>
                    <option value="" hidden selected>Seleccionar región</option>
                    <option value="CENTRAL">Cd. Juárez</option>
                    <option value="WEST">Tijuana</option>
                </select>
            </div>
        </div>
        <div class="col-md-5">
            <div class="input-group flex-grow-1">
                <span class="input-group-text bg-white rounded-end shadow-sm">
                    <img src="<?= asset('public/img/icons/usuarios.svg') ?>" class="icon" alt="">
                    NÚMERO DE EMPLEADO
                </span>
                <input type="number" class="form-control form-control-sm" id="input_cb_codigo" disabled placeholder="# empleado" required>
            </div>
        </div>
        <div class="col-md-3">
            <span class="text-muted small"> <i class="fa-solid fa-circle-info"></i> Ingrese su número de empleado para cargar su información.</span>
        </div>
    </div>

    <div class="row bg-white shadow-sm p-3 rounded mb-3 align-items-center">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label form-label-sm">Nombre</label>
                    <input type="text" class="form-control form-control-sm" disabled id="input_nombre">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label form-label-sm">Género</label>
                    <input type="text" class="form-control form-control-sm" disabled id="input_genero">
                </div>
            </div>
        </div>

        <div class="col-md-1 d-none d-md-flex justify-content-center">
            <div class="vr h-100"></div>
        </div>

        <div class="col-md-5">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label form-label-sm">Estado Civil</label>
                    <input type="text" class="form-control form-control-sm" disabled id="input_estado_civil">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label form-label-sm">Edad</label>
                    <input type="text" class="form-control form-control-sm" disabled id="input_edad">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label form-label-sm">Estudios</label>
                    <input type="text" class="form-control form-control-sm" disabled id="input_nivel_estudios">
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label form-label-sm">Turno</label>
                    <input type="text" class="form-control form-control-sm" disabled id="input_turno">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label form-label-sm">Antigüedad</label>
                    <input type="text" class="form-control form-control-sm" disabled id="input_antiguedad">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label form-label-sm">Área</label>
                    <input type="text" class="form-control form-control-sm" disabled id="input_area">
                </div>
            </div>
        </div>

        <div class="col-md-1 d-none d-md-flex justify-content-center">
            <div class="vr h-100"></div>
        </div>

        <div class="col-md-5">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label form-label-sm">Supervisor</label>
                        <input type="text" class="form-control form-control-sm" disabled id="input_supervisor">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label form-label-sm">Cliente</label>
                        <input type="text" class="form-control form-control-sm" disabled id="input_cliente">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label form-label-sm">Planta</label>
                        <input type="text" class="form-control form-control-sm" disabled id="input_planta">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3" id="div-btnStartQuestionary">
        <div class="col-md-10">
            <span id="spanMessageError"></span>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn  float-end" id="btnVerify" disabled>Comenzar</button>
        </div>
    </div>
</div>