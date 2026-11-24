<?php
$pageTitle = $pageTitle ?? 'Restaurante Online';
$user = current_user();
$flash = get_flash();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | Restaurante Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark app-navbar">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">Sazón &amp; Servicio</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Panel</a></li>
                <li class="nav-item"><a class="nav-link" href="menu.php">Menú</a></li>
                <?php if (in_array($user['role'], ['Administrator', 'Chef', 'Waiter', 'Client'], true)): ?>
                    <li class="nav-item"><a class="nav-link" href="orders.php">Pedidos</a></li>
                <?php endif; ?>
                <?php if ($user['role'] === 'Administrator'): ?>
                    <li class="nav-item"><a class="nav-link" href="users.php">Usuarios</a></li>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center gap-3 text-white">
                <span class="small"><?= e($user['username']) ?> · <?= e(role_label($user['role'])) ?></span>
                <form method="post" action="logout.php" class="m-0">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <button class="btn btn-outline-light btn-sm" type="submit">Salir</button>
                </form>
            </div>
        </div>
    </div>
</nav>

<main class="container py-4">
    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>
