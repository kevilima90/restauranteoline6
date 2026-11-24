
<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$user = current_user();
$role = $user['role'];

if ($role === 'Administrator') {
    $stats = [
        'Pedidos activos' => (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status <> 'Served'")->fetchColumn(),
        'Platos disponibles' => (int)$pdo->query('SELECT COUNT(*) FROM dishes WHERE is_available = 1')->fetchColumn(),
        'Usuarios activos' => (int)$pdo->query('SELECT COUNT(*) FROM users WHERE is_active = 1')->fetchColumn(),
    ];

    $statLinks = [
        'Pedidos activos' => 'orders.php',
        'Platos disponibles' => 'menu.php',
        'Usuarios activos' => 'users.php',
    ];

} elseif ($role === 'Chef') {
    $stats = [
        'Pendientes' => (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'")->fetchColumn(),
        'En preparación' => (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Preparing'")->fetchColumn(),
        'Listos para servir' => (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Ready to serve'")->fetchColumn(),
    ];

    $statLinks = [
        'Pendientes' => 'orders.php',
        'En preparación' => 'orders.php',
        'Listos para servir' => 'orders.php',
    ];

} elseif ($role === 'Waiter') {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE waiter_id = ?');
    $stmt->execute([$user['id']]);
    $assigned = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE waiter_id = ? AND status = 'Ready to serve'");
    $stmt->execute([$user['id']]);
    $ready = (int)$stmt->fetchColumn();

    $stats = [
        'Pedidos asignados' => $assigned,
        'Listos para entregar' => $ready
    ];

    $statLinks = [
        'Pedidos asignados' => 'orders.php',
        'Listos para entregar' => 'orders.php',
    ];

} else {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE user_id = ?');
    $stmt->execute([$user['id']]);
    $total = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND status <> 'Served'");
    $stmt->execute([$user['id']]);
    $active = (int)$stmt->fetchColumn();

    $stats = [
        'Mis pedidos' => $total,
        'Pedidos en curso' => $active
    ];

    $statLinks = [
        'Mis pedidos' => 'orders.php',
        'Pedidos en curso' => 'orders.php',
    ];
}

$pageTitle = 'Panel principal';
require __DIR__ . '/includes/header.php';
?>

<section class="hero-panel mb-4">
    <div>
        <p class="eyebrow">Panel de control</p>
        <h1 class="display-6 fw-bold mb-2">
            Hola, <?= e($user['username']) ?>
        </h1>
        <p class="mb-0">
            Gestione la actividad del restaurante según los permisos de su perfil.
        </p>
    </div>
    <div class="hero-icon" aria-hidden="true">✦</div>
</section>

<div class="row g-3 mb-4">
    <?php foreach ($stats as $label => $value): ?>
        <div class="col-12 col-md-6 col-xl-4">
            <a
                href="<?= e($statLinks[$label]) ?>"
                class="stat-card h-100 d-block text-decoration-none text-reset"
                style="cursor:pointer;"
                title="Abrir <?= e($label) ?>"
            >
                <div class="stat-number">
                    <?= e($value) ?>
                </div>
                <div class="stat-label">
                    <?= e($label) ?>
                </div>
                <small class="text-secondary mt-2 d-block">
                    Ver detalles →
                </small>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="content-card h-100">
            <h2 class="h5 fw-bold">Consultar el menú</h2>
            <p class="text-secondary">
                Revise los platos, sus descripciones y precios disponibles.
            </p>
            <a href="menu.php" class="btn btn-brand">Ver menú</a>
        </div>
    </div>

    <div class="col-md-6">
        <div class="content-card h-100">
            <h2 class="h5 fw-bold">Gestionar pedidos</h2>
            <p class="text-secondary">
                Consulte el estado de los pedidos y las acciones habilitadas para su rol.
            </p>
            <a href="orders.php" class="btn btn-outline-brand">Ver pedidos</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
