<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_role('Administrator');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('users.php');
}
verify_csrf();

$id = (int)($_POST['id'] ?? 0);
$username = post_string('username', 50);
$email = trim((string)($_POST['email'] ?? ''));
$roleId = (int)($_POST['role_id'] ?? 0);
$passwordInput = (string)($_POST['password'] ?? '');
$isActive = isset($_POST['is_active']) ? 1 : 0;

if ($username === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $roleId < 1) {
    set_flash('danger', 'Complete el nombre, correo válido y rol.');
    redirect('users.php');
}

$stmt = $pdo->prepare('SELECT id FROM roles WHERE id = ?');
$stmt->execute([$roleId]);
if (!$stmt->fetch()) {
    set_flash('danger', 'El rol seleccionado no existe.');
    redirect('users.php');
}

$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id <> ?');
$stmt->execute([$email, $id]);
if ($stmt->fetch()) {
    set_flash('danger', 'Ese correo electrónico ya está registrado.');
    redirect($id > 0 ? 'users.php?edit=' . $id : 'users.php');
}

if ($id > 0) {
    if ($passwordInput !== '' && strlen($passwordInput) < 8) {
        set_flash('danger', 'La contraseña debe tener al menos 8 caracteres.');
        redirect('users.php?edit=' . $id);
    }

    if ($passwordInput !== '') {
        $hash = password_hash($passwordInput, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ?, role_id = ?, password = ?, is_active = ? WHERE id = ?');
        $stmt->execute([$username, $email, $roleId, $hash, $isActive, $id]);
    } else {
        $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ?, role_id = ?, is_active = ? WHERE id = ?');
        $stmt->execute([$username, $email, $roleId, $isActive, $id]);
    }
    set_flash('success', 'La cuenta se actualizó correctamente.');
} else {
    if (strlen($passwordInput) < 8) {
        set_flash('danger', 'La contraseña debe tener al menos 8 caracteres.');
        redirect('users.php');
    }
    $hash = password_hash($passwordInput, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (username, email, password, role_id, is_active) VALUES (?, ?, ?, ?, 1)');
    $stmt->execute([$username, $email, $hash, $roleId]);
    set_flash('success', 'La cuenta se creó correctamente.');
}

redirect('users.php');
