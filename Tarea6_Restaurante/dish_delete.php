<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_role('Administrator');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('menu.php');
}
verify_csrf();

$id = (int)($_POST['id'] ?? 0);
$stmt = $pdo->prepare('UPDATE dishes SET is_available = 0 WHERE id = ?');
$stmt->execute([$id]);

set_flash('success', 'El plato fue retirado del menú. Su historial de pedidos se conserva.');
redirect('menu.php');
