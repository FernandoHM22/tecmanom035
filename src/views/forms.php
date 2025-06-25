<?php
$titleHeader = 'NOM 035 - ' . Date('Y');
$pageId = 'forms';
include 'partials/header_forms.php';
?>
<div id="containerQuestionaries">
    <div class="row headersTitles align-items-center mt-4 p-2">
        <!-- <div class="col-auto text-center fw-bold" id="titleApp">NOM 035</div> -->
        <div class="col-4" id="clientName"></div>
        <div class="col" id="titleGuide">
            <span class="float-end"><img src="<?= asset('public/img/icons/guia.svg') ?>" alt="Icono guia" class="icon"></span>
        </div>
    </div>

    <div id="div-instructions"></div>
    <div id="div-indications" class="bg-white rounded p-2 mt-3 d-flex justify-content-between align-items-center d-none">
        <p class="mb-0" id="indicationsText">
            <img src="<?= asset('public/img/icons/info_icon.svg') ?>" class="icon" alt="">
            Favor de responder con la mayor honestidad posible, considerando los últimos 3 meses de trabajo en esta empresa.
        </p>
    </div>

    <div id="div-forms" class=" row d-none mt-3">
        <form id="form-questions">
            <div id="question-container"></div>
            <div class="mt-3 d-flex justify-content-between">
                <div>
                    <button type="button" id="prevQuestionBtn" class="btn btn-secondary d-none">← Anterior</button>
                </div>
                <div>
                    <button type="button" id="nextQuestionBtn" class="btn btn-success">Siguiente →</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php include 'partials/footer.php'; ?>