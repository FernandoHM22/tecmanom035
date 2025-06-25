<?php
$titleHeader = 'NOM 035 - ' . Date('Y');
$pageId = 'admin-schedule';
$currentPage = 'Programar Encuestas';
include __DIR__ . '/../partials/header_admin.php';
include __DIR__ . '/../partials/sidebar.php';
?>

<div id="main-content">
    <div class="container-fluid" id="main-content-schedule">
        <div class="row">
            <!-- Columna izquierda -->
            <div class="col-md-3">
                <!-- Selector Cliente -->
                <div class="mb-3">
                    <span id="client-selector">Cliente</span>
                    <ul class="list-group mt-2" id="client-list">

                    </ul>
                </div>
                <!-- Fecha / Mes / Guía -->
                <div class="custom-field mb-3">
                    <span class="custom-label">Fecha de aplicación</span>
                    <input type="date" class="form-control form-control-sm shadow-sm" id="input-date-application" />
                </div>

                <div class="custom-field mb-3">
                    <span class="custom-label">Mes</span>
                    <select class="form-select form-select-sm shadow-sm" id="select-month"></select>
                </div>

                <div class="custom-field mb-3">
                    <span class="custom-label">Guía</span>
                    <select class="form-select form-select-sm shadow-sm" id="select-guide">
                        <option selected hidden>Seleccione de guía</option>
                        <option value="2">Guía 2</option>
                        <option value="3">Guía 3</option>
                    </select>
                </div>

            </div>

            <!-- Columna principal -->
            <div class="col-md-9">
                <div class="row">
                    <!-- Datos RH -->
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded shadow-sm bg-light">
                            <h6 class="fw-bold">DATOS PARA RH</h6>
                            <!-- Inputs aquí -->
                        </div>
                    </div>

                    <!-- Generados automático -->
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded shadow-sm bg-light">
                            <h6 class="fw-bold">GENERADOS EN AUTOMÁTICO</h6>
                            <!-- Inputs aquí -->
                        </div>
                    </div>

                    <!-- Servicios -->
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded shadow-sm bg-light">
                            <h6 class="fw-bold">SERVICIOS</h6>
                            <div id="service-list">

                            </div>
                        </div>
                    </div>

                    <!-- Turnos / Abarca / Hombres / Mujeres -->
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded shadow-sm bg-light">
                            <div class="row">
                                <div class="col">Turno</div>
                                <div class="col">Abarca</div>
                                <div class="col">Hombres</div>
                                <div class="col">Mujeres</div>
                            </div>
                            <!-- Más filas -->
                        </div>
                    </div>

                    <!-- Logotipo y Link -->
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded shadow-sm bg-light">
                            <p class="fw-bold">LOGOTIPO ACTUAL</p>
                            <img src="" style="height: 40px">
                            <div>
                                <label class="form-check-label me-2">¿Es correcto?</label>
                                <input type="radio" name="logo" checked> Sí
                                <input type="radio" name="logo"> No
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded shadow-sm bg-light">
                            <p class="fw-bold">GENERAR LINK</p>
                            <input type="text" class="form-control">
                        </div>
                    </div>

                    <!-- Selectores y botón guardar -->
                    <div class="col-md-12 d-flex justify-content-between align-items-center">
                        <div>
                            <button class="btn btn-outline-danger rounded-pill me-2" data-url="modal_sample_client" data-modal-handler="sample" id="btnSample"> <img src="<?= asset('public/img/icons/sample.svg') ?>" class="icon">Muestra</button>
                            <!-- <button class="btn btn-outline-danger rounded-pill me-2">Dpto.</button>
                            <button class="btn btn-outline-danger rounded-pill">Líderes</button> -->
                        </div>
                        <div>
                            <button class="btn btn-tecma btn-lg rounded-pill px-4 shadow">Guardar</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>



<?php include __DIR__ . '/../partials/footer.php'; ?>