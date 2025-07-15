<?php
$titleHeader = 'NOM 035 - ' . Date('Y');
$pageId = 'admin-schedule';
$currentPage = 'Programar Encuestas';
include __DIR__ . '/../partials/header_admin.php';
include __DIR__ . '/../partials/sidebar.php';
?>

<div id="main-content">
    <div class="container-fluid" id="main-content-schedule">
        <div class="row mb-2">
            <div class="col-md-12">
                <button id="btnSaveSurvey" class="btn btn-sm px-3 shadow float-end">
                    Guardar
                </button>
            </div>
        </div>
        <div class="row">
            <!-- Columna izquierda -->
            <div class="col-md-3">
                <!-- Selector Cliente -->
                <div class="mb-3">
                    <span id="client-selector">Cliente</span>
                    <input type="text" id="searchProject" class="form-control form-control-sm mb-2 mt-1" placeholder="Buscar proyecto...">
                    <ul class="list-group mt-2" id="client-list"></ul>
                </div>
                <!-- Fecha / Mes / Guía -->
                <div class="custom-field mb-3">
                    <span class="custom-label">Fecha de aplicación</span>
                    <input type="date" class="form-control form-control-sm shadow-sm" id="input-date-application" />
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
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12 mb-2" id="rh-data">
                                <div class="p-3 border rounded shadow-sm bg-light">
                                    <h6 class="fw-bold">DATOS PARA RH</h6>
                                    <div class="col-md-12">
                                        <label for="input_gte" class="form-label">RESP. OPERACIÓN / GTE PLANTA</label>
                                        <input type="text" class="form-control form-control-sm" placeholder="Ingrese nombre" id="input_gte" disabled required>
                                    </div>
                                </div>
                            </div>
                            <!-- Servicios -->
                            <div class="col-md-12 mb-2">
                                <div class="p-3 border rounded shadow-sm bg-light">
                                    <h6 class="fw-bold">SERVICIOS</h6>
                                    <div id="service-list"></div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2 d-none" id="logos-container">
                                <div class="p-3 border rounded shadow-sm bg-light">
                                    <div class="logo-section">
                                        <div>
                                            <p class="fw-bold mb-2">LOGOTIPO</p>
                                            <img src="" id="project-logo" class="logo-image">
                                        </div>
                                        <div>
                                            <label class="form-check-label me-2 d-block">¿Es correcto?</label>
                                            <div class="custom-radio">
                                                <input type="radio" id="logo-yes" name="logo" value="yes" checked>
                                                <label for="logo-yes">Sí</label>

                                                <input type="radio" id="logo-no" data-url="modal_change_logo" data-modal-handler="changeLogo" name="logo" value="no">
                                                <label for="logo-no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-12" style="max-height: 580px; overflow-y: auto;">
                            <!-- CONTENEDOR PRINCIPAL -->
                            <div class="p-4 border rounded shadow-sm bg-light">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="fw-bold mb-0">GENERADOR</h5>
                                    <button class="btn btn-danger rounded-pill" data-url="modal_sample_client" data-modal-handler="sample" disabled id="btnSample">
                                        <img src="<?= asset('public/img/icons/sample2.svg') ?>" class="icon me-1">Generar muestra
                                    </button>
                                </div>

                                <p class="text-muted fst-italic mb-2">
                                    Presiona el botón <strong>"Generar muestra"</strong> para crear los datos que se mostrarán a continuación.
                                </p>
                                <div id="generalSampleFactorContainer" class="rounded-pill bg-secondary bg-opacity-25 mb-2 d-none">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold text-muted" id="title">Factor general de muestra</span>
                                        <span id="generalSampleFactor" class="badge-pill-custom"></span>
                                    </div>
                                </div>

                                <!-- NAV TABS -->
                                <ul class="nav nav-tabs nav-fill justify-content-center mb-2" id="sampleTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active unopened-tab" id="areas-tab" data-bs-toggle="tab" data-bs-target="#tab-areas" type="button" role="tab">Áreas</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link unopened-tab" id="shifts-tab" data-bs-toggle="tab" data-bs-target="#tab-shifts" type="button" role="tab">Turnos</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link unopened-tab" id="supervisors-tab" data-bs-toggle="tab" data-bs-target="#tab-supervisors" type="button" role="tab">Supervisores</button>
                                    </li>
                                </ul>

                                <!-- CONTENIDO DE CADA PESTAÑA -->
                                <div class="tab-content" id="sampleTabsContent">
                                    <!-- TAB ÁREAS -->
                                    <div class="tab-pane fade show active" id="tab-areas" role="tabpanel">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover table-sm table-striped mb-0" id="tableAreasSample">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Área</th>
                                                        <th>Colaboradores</th>
                                                        <th>Hombres</th>
                                                        <th>Mujeres</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- TAB TURNOS -->
                                    <div class="tab-pane fade" id="tab-shifts" role="tabpanel">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover table-sm table-striped mb-0" id="tableShiftsSample">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Turno</th>
                                                        <th>Colaboradores</th>
                                                        <th>Hombres</th>
                                                        <th>Mujeres</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- TAB SUPERVISORES -->
                                    <div class="tab-pane fade" id="tab-supervisors" role="tabpanel">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover table-sm table-striped mb-0" id="tableSupervisorsSample">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Supervisor</th>
                                                        <th>Colaboradores</th>
                                                        <th>Hombres</th>
                                                        <th>Mujeres</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<?php include __DIR__ . '/../partials/footer.php'; ?>