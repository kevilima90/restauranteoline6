<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_role('Administrator', 'Waiter', 'Client');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('orders.php');
}
verify_csrf();

$user = current_user();
$role = $user['role'];
$dishId = (int)($_POST['dish_id'] ?? 0);
$quantity = filter_var($_POST['quantity'] ?? null, FILTER_VALIDATE_INT);

if ($dishId < 1 || $quantity === false || $quantity < 1 || $quantity > 50) {
    set_flash('danger', 'Seleccione un plato y una cantidad entre 1 y 50.');
    redirect('order_create.php');
}

$stmt = $pdo->prepare('SELECT id FROM dishes WHERE id = ? AND is_available = 1');
$stmt->execute([$dishId]);
if (!$stmt->fetch()) {
    set_flash('danger', 'El plato seleccionado ya no está disponible.');
    redirect('menu.php');
}

if ($role === 'Client') {
    $clientId = (int)$user['id'];
    $waiterId = null;
} else {
    $clientId = (int)($_POST['client_id'] ?? 0);
    $waiterId = $role === 'Waiter' ? (int)$user['id'] : null;

    $stmt = $pdo->prepare(
        "SELECT u.id FROM users u INNER JOIN roles r ON r.id = u.role_id
         WHERE u.id = ? AND r.name = 'Client' AND u.is_active = 1"
    );
    $stmt->execute([$clientId]);
    if (!$stmt->fetch()) {
        set_flash('danger', 'Seleccione un cliente activo válido.');
        redirect('order_create.php');
    }
}

$stmt = $pdo->prepare(
    "INSERT INTO orders (user_id, dish_id, quantity, status, waiter_id)
     VALUES (?, ?, ?, 'Pending', ?)"
);
$stmt->execute([$clientId, $dishId, $quantity, $waiterId]);

set_flash('success', 'El pedido se registró correctamente y quedó en estado Pendiente.');
redirect('orders.php');
