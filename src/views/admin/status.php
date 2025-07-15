<?php
$titleHeader = 'NOM 035 - ' . Date('Y');
$pageId = 'admin-status';
$currentPage = 'Estatus Encuestas';
include __DIR__ . '/../partials/header_admin.php';
include __DIR__ . '/../partials/sidebar.php';
?>

<div id="main-content">
    <div class="container-fluid" id="main-content-status">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Estatus de Encuestas</h2>
                <p>En esta sección puedes ver el estatus de las encuestas programadas.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
               <div id="status-table-container"></div>
            </div>
        </div>
    </div>
</div>


<?php include __DIR__ . '/../partials/footer.php'; ?>