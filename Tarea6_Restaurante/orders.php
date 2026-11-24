<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$user = current_user();
$role = $user['role'];

$sql = "SELECT o.*, d.name AS dish_name, d.price,
               client.username AS client_name,
               waiter.username AS waiter_name
        FROM orders o
        INNER JOIN dishes d ON d.id = o.dish_id
        INNER JOIN users client ON client.id = o.user_id
        LEFT JOIN users waiter ON waiter.id = o.waiter_id";

if ($role === 'Client') {
    $sql .= ' WHERE o.user_id = :user_id';
} elseif ($role === 'Waiter') {
    $sql .= ' WHERE o.waiter_id = :user_id';
}
$sql .= ' ORDER BY o.created_at DESC';

$stmt = $pdo->prepare($sql);
if (in_array($role, ['Client', 'Waiter'], true)) {
    $stmt->execute(['user_id' => $user['id']]);
} else {
    $stmt->execute();
}
$orders = $stmt->fetchAll();

$pageTitle = 'Pedidos';
require __DIR__ . '/includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <p class="eyebrow mb-1">Seguimiento</p>
        <h1 class="h2 fw-bold mb-0"><?= $role === 'Client' ? 'Historial de mis pedidos' : 'Gestión de pedidos' ?></h1>
    </div>
    <?php if (in_array($role, ['Administrator', 'Waiter', 'Client'], true)): ?>
        <a href="order_create.php" class="btn btn-brand">+ Nuevo pedido</a>
    <?php endif; ?>
</div>

<div class="content-card">
    <?php if (!$orders): ?>
        <div class="empty-state">
            <div class="display-6 mb-2">☕</div>
            <h2 class="h5">No hay pedidos registrados</h2>
            <p class="text-secondary mb-0">Cuando se registre un pedido, aparecerá en esta sección.</p>
        </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table align-middle app-table">
            <thead>
                <tr>
                    <th>N.º</th>
                    <?php if ($role !== 'Client'): ?><th>Cliente</th><?php endif; ?>
                    <th>Plato</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <?php if ($role !== 'Client'): ?><th>Camarero</th><?php endif; ?>
                    <th>Fecha</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td class="fw-semibold">#<?= (int)$order['id'] ?></td>
                    <?php if ($role !== 'Client'): ?><td><?= e($order['client_name']) ?></td><?php endif; ?>
                    <td><?= e($order['dish_name']) ?></td>
                    <td><?= (int)$order['quantity'] ?></td>
                    <td><?= e(money((float)$order['price'] * (int)$order['quantity'])) ?></td>
                    <td><span class="badge text-bg-<?= e(status_badge($order['status'])) ?>"><?= e(status_label($order['status'])) ?></span></td>
                    <?php if ($role !== 'Client'): ?><td><?= e($order['waiter_name'] ?: 'Sin asignar') ?></td><?php endif; ?>
                    <td class="small text-secondary"><?= e(date('d/m/Y H:i', strtotime($order['created_at']))) ?></td>
                    <td>
                        <?php if ($role === 'Chef' && $order['status'] === 'Pending'): ?>
                            <form method="post" action="order_status.php">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                                <input type="hidden" name="status" value="Preparing">
                                <button class="btn btn-sm btn-outline-brand" type="submit">Preparar</button>
                            </form>
                        <?php elseif ($role === 'Chef' && $order['status'] === 'Preparing'): ?>
                            <form method="post" action="order_status.php">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                                <input type="hidden" name="status" value="Ready to serve">
                                <button class="btn btn-sm btn-outline-brand" type="submit">Marcar listo</button>
                            </form>
                        <?php elseif ($role === 'Waiter' && $order['status'] === 'Ready to serve'): ?>
                            <form method="post" action="order_status.php">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                                <input type="hidden" name="status" value="Served">
                                <button class="btn btn-sm btn-success" type="submit">Entregado</button>
                            </form>
                        <?php elseif ($role === 'Administrator' && $order['status'] !== 'Served'): ?>
                            <form method="post" action="order_status.php" class="d-flex gap-1">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                                <select class="form-select form-select-sm" name="status" aria-label="Cambiar estado">
                                    <?php foreach (['Pending', 'Preparing', 'Ready to serve', 'Served'] as $status): ?>
                                        <option value="<?= e($status) ?>" <?= $order['status'] === $status ? 'selected' : '' ?>><?= e(status_label($status)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-sm btn-brand" type="submit">OK</button>
                            </form>
                        <?php else: ?>
                            <span class="text-secondary small">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
