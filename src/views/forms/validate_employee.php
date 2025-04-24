<div id="containerValidateEmployee">
    <div class="row mt-2 mb-3">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <!-- Grupo de input con icono -->
                <div class="input-group flex-grow-1">
                    <span class="input-group-text bg-white rounded-end shadow-sm">
                        <img src="../../../public/img/icons/usuarios.svg" class="icon" alt="">
                        NÚMERO DE EMPLEADO
                    </span>
                    <input type="number" class="form-control" id="input_cb_codigo" placeholder="# empleado" required>
                </div>

                <!-- Texto de ayuda a la derecha -->
                <span class="ms-3 text-muted small">Ingrese su número de empleado para cargar su información</span>
            </div>
        </div>
    </div>


    <div class="row shadow bg-white py-3 rounded-4">
        <div class="row">
            <div class="col">
                <div class="form-floating">
                    <input type="text" class="form-control" id="inputNames" value="" disabled>
                    <label for="inputNames">Nombres</label>
                </div>
            </div>
            <div class="col">
                <div class="form-floating">
                    <input type="text" class="form-control" id="inputLastnames" value="" disabled>
                    <label for="inputLastnames">Apellidos</label>
                </div>
            </div>
            <div class="col">
                <div class="form-floating">
                    <input type="text" class="form-control" id="inputGender" value="" disabled>
                    <label for="inputGender">Género</label>
                </div>
            </div>
            <div class="col">
                <div class="form-floating">
                    <input type="text" class="form-control" id="inputCivilStatus" value="" disabled>
                    <label for="inputCivilStatus">Estado Civil</label>
                </div>
            </div>
            <div class="col">
                <div class="form-floating">
                    <input type="text" class="form-control" id="inputAgeRange" value="" disabled>
                    <label for="inputAgeRange">Rango Edad</label>
                </div>
            </div>
        </div>
    </div>
    <div class="row shadow bg-white py-3 mt-4 rounded-4">
        <div class="row">
            <div class="col">
                <div class="form-floating">
                    <input type="text" class="form-control" id="inputShift" value="" disabled>
                    <label for="inputShift">Turno</label>
                </div>
            </div>
            <div class="col">
                <div class="form-floating">
                    <input type="text" class="form-control" id="inputSeniority" value="" disabled>
                    <label for="inputSeniority">Antiguedad</label>
                </div>
            </div>
            <div class="col">
                <div class="form-floating">
                    <input type="text" class="form-control" id="inputArea" value="" disabled>
                    <label for="inputArea">Área</label>
                </div>
            </div>
            <div class="col">
                <div class="form-floating">
                    <input type="text" class="form-control" id="inputSupervisor" value="" disabled>
                    <label for="inputSupervisor">Supervisor</label>
                </div>
            </div>
        </div>
    </div>
</div>