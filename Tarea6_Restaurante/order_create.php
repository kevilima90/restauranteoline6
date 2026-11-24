<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_role('Administrator', 'Waiter', 'Client');

$dishes = $pdo->query('SELECT id, name, price FROM dishes WHERE is_available = 1 ORDER BY name')->fetchAll();
$role = current_user()['role'];
$clients = [];

if (in_array($role, ['Administrator', 'Waiter'], true)) {
    $stmt = $pdo->query(
        "SELECT u.id, u.username, u.email
         FROM users u INNER JOIN roles r ON r.id = u.role_id
         WHERE r.name = 'Client' AND u.is_active = 1
         ORDER BY u.username"
    );
    $clients = $stmt->fetchAll();
}

$selectedDish = (int)($_GET['dish_id'] ?? 0);
$pageTitle = 'Registrar pedido';
require __DIR__ . '/includes/header.php';
?>
<div class="mb-4">
    <p class="eyebrow mb-1">Servicio</p>
    <h1 class="h2 fw-bold">Registrar pedido</h1>
    <p class="text-secondary mb-0">
        <?= $role === 'Client' ? 'Seleccione un plato para realizar su pedido.' : 'Registre un pedido en nombre de un cliente.' ?>
    </p>
</div>

<div class="content-card form-card">
    <?php if (!$dishes): ?>
        <div class="alert alert-warning mb-0">No hay platos disponibles en este momento.</div>
    <?php else: ?>
    <form method="post" action="order_save.php" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <?php if (in_array($role, ['Administrator', 'Waiter'], true)): ?>
            <div class="col-12">
                <label for="client_id" class="form-label">Cliente</label>
                <select class="form-select" id="client_id" name="client_id" required>
                    <option value="">Seleccione un cliente</option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?= (int)$client['id'] ?>"><?= e($client['username']) ?> (<?= e($client['email']) ?>)</option>
                    <?php endforeach; ?>
                </select>
                <?php if (!$clients): ?><div class="form-text text-danger">No hay clientes activos. El administrador debe crear una cuenta de cliente.</div><?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="col-md-7">
            <label for="dish_id" class="form-label">Plato</label>
            <select class="form-select" id="dish_id" name="dish_id" required>
                <option value="">Seleccione un plato</option>
                <?php foreach ($dishes as $dish): ?>
                    <option value="<?= (int)$dish['id'] ?>" data-price="<?= e($dish['price']) ?>" <?= $selectedDish === (int)$dish['id'] ? 'selected' : '' ?>>
                        <?= e($dish['name']) ?> — <?= e(money($dish['price'])) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-5">
            <label for="quantity" class="form-label">Cantidad</label>
            <input class="form-control" id="quantity" name="quantity" type="number" min="1" max="50" value="1" required>
        </div>
        <div class="col-12">
            <div class="order-preview" id="orderPreview">Seleccione un plato para visualizar el subtotal.</div>
        </div>
        <div class="col-12 d-flex gap-2">
            <button class="btn btn-brand" type="submit" <?= (in_array($role, ['Administrator', 'Waiter'], true) && !$clients) ? 'disabled' : '' ?>>Confirmar pedido</button>
            <a href="orders.php" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
