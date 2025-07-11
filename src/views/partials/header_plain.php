<?php
require_once dirname(__DIR__, 3) . '/config/config.php';
include dirname(__DIR__, 1) . '/partials/head_common.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>APP NOM035 | <?= $titleHeader ?></title>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="<?= asset('public/img/favicon/favicon-96x96.png') ?>" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="<?= asset('public/img/favicon/favicon.svg') ?>" />
    <link rel="shortcut icon" href="<?= asset('public/img/favicon/favicon.ico') ?>" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <link rel="manifest" href="<?= asset('public/img/favicon/site.webmanifest') ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= asset('public/css/auth.css') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css" rel="stylesheet">
</head>

<body data-page="<?= $pageId ?? '' ?>">