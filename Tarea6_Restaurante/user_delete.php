<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_role('Administrator');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('users.php');
}
verify_csrf();

$id = (int)($_POST['id'] ?? 0);
if ($id < 1 || $id === (int)current_user()['id']) {
    set_flash('danger', 'No puede desactivar su propia cuenta desde esta pantalla.');
    redirect('users.php');
}

$stmt = $pdo->prepare('UPDATE users SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?');
$stmt->execute([$id]);

set_flash('success', 'El estado de la cuenta se modificó correctamente.');
redirect('users.php');
