<?php
$pageId = 'auth-register';
$titleHeader = 'Registro';
include __DIR__ . '/../partials/header_plain.php';
?>

<div class="register-container">
    <h4 class="mb-4 text-center">Registro de Usuario</h4>
    <div class="mb-3">
        <label for="fullName" class="form-label">Nombre completo</label>
        <input type="text" class="form-control" id="fullName" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Correo electrónico</label>
        <input type="email" class="form-control" id="email" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" class="form-control" id="password" required>
    </div>
    <div class="mb-3">
        <label for="region" class="form-label">Región</label>
        <select class="form-control" id="region" required>
            <option value="CENTRAL">Central</option>
            <option value="WEST">West</option>
            <option value="EAST">East</option>
        </select>
    </div>
    <div class="text-center">
        <button class="btn btn-success rounded-pill py-1 px-4" type="button" id="register-button">Registrar</button>
    </div>

</div>


<?php include __DIR__ . '/../partials/footer.php'; ?>