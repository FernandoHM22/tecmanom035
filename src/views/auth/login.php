<?php
$pageId = 'auth-login';
$titleHeader = 'Iniciar Sesión';
include __DIR__ . '/../partials/header_plain.php';
?>
<div class="container-fluid login-container min-vh-100">
    <div class="row h-100">
        <di class="col-md-5 login-left h-100"></di>
        <di class="col-md-7 login-right h-100">
            <div class="top-nav">
                <img src="<?= asset('/public/img/logo-tecma.png') ?>" alt="TECMA">
            </div>


            <img src="<?= asset('/public/img/logoNOM035Cuestionarios.png') ?>" alt="LOGO NOM035" width="500" class="mb-3">


            <form id="loginForm" class="login-form w-100" style="max-width: 450px; margin-top:20px">
                <input type="email" id="email-user" class="form-control" placeholder="Correo" required>
                <input type="password" id="password-user" class="form-control" placeholder="Contraseña" required>
                <div class="text-center">
                    <button type="button" class="btn" id="login-button">INICIAR SESION</button>
                </div>
            </form>
        </di>
    </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>