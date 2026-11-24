<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_role('Administrator', 'Chef', 'Waiter');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('orders.php');
}
verify_csrf();

$orderId = (int)($_POST['order_id'] ?? 0);
$newStatus = (string)($_POST['status'] ?? '');
$validStatuses = ['Pending', 'Preparing', 'Ready to serve', 'Served'];

if ($orderId < 1 || !in_array($newStatus, $validStatuses, true)) {
    set_flash('danger', 'Solicitud de actualización no válida.');
    redirect('orders.php');
}

$stmt = $pdo->prepare('SELECT id, status, waiter_id FROM orders WHERE id = ?');
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    set_flash('danger', 'El pedido no existe.');
    redirect('orders.php');
}

$role = current_user()['role'];
$allowed = false;

if ($role === 'Administrator') {
    $allowed = true;
} elseif ($role === 'Chef') {
    $allowed = ($order['status'] === 'Pending' && $newStatus === 'Preparing')
        || ($order['status'] === 'Preparing' && $newStatus === 'Ready to serve');
} elseif ($role === 'Waiter') {
    $allowed = ((int)$order['waiter_id'] === (int)current_user()['id'])
        && $order['status'] === 'Ready to serve'
        && $newStatus === 'Served';
}

if (!$allowed) {
    http_response_code(403);
    exit('No tiene permisos para realizar esta transición de estado.');
}

$stmt = $pdo->prepare('UPDATE orders SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
$stmt->execute([$newStatus, $orderId]);

set_flash('success', 'El estado del pedido se actualizó a: ' . status_label($newStatus) . '.');
redirect('orders.php');
