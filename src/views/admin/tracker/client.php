<?php
$titleHeader = 'NOM 035 - ' . Date('Y');
$pageId = 'admin-client-tracker';
$currentPage = 'Seguimiento Clientes';

include __DIR__ . '/../../partials/header_admin.php';
include __DIR__ . '/../../partials/sidebar.php';
?>
<div id="main-content">
    <div class="container-fluid" id="main-content-client-tracker">
        <div class="container py-4">

            <!-- 🔽 Select de cliente -->
            <div class="row">
                <div class="col-md-4">
                    <label for="client-configured-select" class="form-label">Seleccionar Cliente</label>
                    <select id="client-configured-select" class="form-select">
                        <option selected disabled hidden>Seleccione un cliente</option>
                        <!-- Opciones generadas dinámicamente -->
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="years-select" class="form-label">Seleccionar Periodo</label>
                    <select id="years-select" class="form-select">
                        <option selected disabled hidden>Seleccione un periodo</option>
                        <!-- Opciones generadas dinámicamente -->
                    </select>
                </div>
                <div class="col-md-4 float-end d-flex align-items-end">
                    <button type="button" id="btn-refresh-data" class="btn btn-sm btn-primary">Actualizar</button>
                </div>
            </div>

            <!-- 🧩 Tablas -->
            <div class="row mt-2 g-4" id="tables-tracker-container">
                <!-- Tabla General -->
                <div class="col-12">
                    <h5>Seguimiento General</h5>
                    <div class="table-responsive">
                        <table class="table align-middle tracker-table" id="table-general-client-tracker">
                            <thead class="table-light">
                                <tr>
                                    <th>Total Muestra</th>
                                    <th># Muestra</th>
                                    <th>% Muestra</th>
                                    <th>Total Muestra Hombres</th>
                                    <th># Muestra Hombres</th>
                                    <th>Total Muestra Mujeres</th>
                                    <th># Muestra Mujeres</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <!-- Tabla por Área -->
                <div class="col-12">
                    <h5>Seguimiento por Área</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle tracker-table" id="table-area-client-tracker">
                            <thead class="table-light">
                                <tr>
                                    <th>Área</th>
                                    <th>Total Muestra</th>
                                    <th>Muestra Completada </th>
                                    <th>Muestra Hombres</th>
                                    <th>Hombres Completados</th>
                                    <th>Muestra Mujeres</th>
                                    <th>Mujeres Completadas</th>
                                    <th>Completado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Renglones dinámicos -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tabla por Supervisor -->
                <div class="col-12">
                    <h5>Seguimiento por Supervisor</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle tracker-table" id="table-supervisor-client-tracker">
                            <thead class="table-light">
                                <tr>
                                    <th>Supervisor</th>
                                    <th>Total Muestra</th>
                                    <th>Muestra Completada </th>
                                    <th>Muestra Hombres</th>
                                    <th>Hombres Completados</th>
                                    <th>Muestra Mujeres</th>
                                    <th>Mujeres Completadas</th>
                                    <th>Completado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Renglones dinámicos -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tabla por Turno -->
                <div class="col-12">
                    <h5>Seguimiento por Turno</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle tracker-table" id="table-workshift-client-tracker">
                            <thead class="table-light">
                                <tr>
                                    <th>Turno</th>
                                    <th>Total Muestra</th>
                                    <th>Muestra Completada </th>
                                    <th>Muestra Hombres</th>
                                    <th>Hombres Completados</th>
                                    <th>Muestra Mujeres</th>
                                    <th>Mujeres Completadas</th>
                                    <th>Completado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Renglones dinámicos -->
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>


<?php include __DIR__ . '/../../partials/footer.php'; ?>