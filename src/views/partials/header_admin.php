<?php
require_once dirname(__DIR__, 3) . '/config/config.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>APP NOM035 | PROGRAMACIÓN</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?= asset('public/img/favicon/favicon-96x96.png') ?>" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="<?= asset('public/img/favicon/favicon.svg') ?>" />
    <link rel="shortcut icon" href="<?= asset('public/img/favicon/favicon.ico') ?>" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <link rel="manifest" href="<?= asset('public/img/favicon/site.webmanifest') ?>" />
    <link rel="stylesheet" href="<?= asset('public/css/global.css') ?>">
    <link rel="stylesheet" href="<?= asset('public/css/sidebar.css') ?>">
    <link rel="stylesheet" href="<?= asset('public/css/modals.css') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css" rel="stylesheet">
</head>

<body data-page="<?= $pageId ?? '' ?>" class="protected">

    <div class="navbar-top d-flex align-items-center justify-content-between px-3" id="navbar-top">
        <button id="btnMenu" class="btn d-flex align-items-center shadow-sm rounded-pill text-center" style="background: white; width: 235px;">
            <i class="fa-solid fa-bars me-2 text-tecma"></i> <span class="text-tecma fw-semibold">MENU</span> <a href=""></a>
        </button>

        <div class="d-flex align-items-center ms-auto gap-3">
            <div class="dropdown" id="user-pill">
                <button class="btn dropdown-toggle d-flex align-items-center bg-white shadow-sm px-4 py-1"
                    id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?= asset('public/img/default-user.png') ?>" alt="Usuario" width="40" height="40" class="rounded-circle me-2">
                    <div class="text-start small">
                        <strong id="name"></strong><br>
                        <span class="text-muted" id="userRole"></span>
                    </div>
                </button>

                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><span class="dropdown-item-text"><strong id="userEmail"></strong></span></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" href="#" id="logoutButton"><i class="fa-solid fa-right-from-bracket me-2"></i>Cerrar sesión</a></li>
                </ul>
            </div>
            <img src="<?= asset('public/img/logo-tecma.png') ?>" alt="TECMA" height="30">
        </div>
    </div>