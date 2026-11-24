<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_role('Administrator');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('menu.php');
}
verify_csrf();

$id = (int)($_POST['id'] ?? 0);
$name = post_string('name', 50);
$description = post_string('description', 255);
$price = filter_var($_POST['price'] ?? null, FILTER_VALIDATE_FLOAT);
$isAvailable = isset($_POST['is_available']) ? 1 : 0;

if ($name === '' || $price === false || $price <= 0) {
    set_flash('danger', 'Ingrese un nombre y un precio válido mayor que cero.');
    redirect('menu.php');
}

if ($id > 0) {
    $stmt = $pdo->prepare('UPDATE dishes SET name = ?, description = ?, price = ?, is_available = ? WHERE id = ?');
    $stmt->execute([$name, $description, $price, $isAvailable, $id]);
    set_flash('success', 'El plato se actualizó correctamente.');
} else {
    $stmt = $pdo->prepare('INSERT INTO dishes (name, description, price, is_available) VALUES (?, ?, ?, 1)');
    $stmt->execute([$name, $description, $price]);
    set_flash('success', 'El plato se registró correctamente.');
}

redirect('menu.php');
