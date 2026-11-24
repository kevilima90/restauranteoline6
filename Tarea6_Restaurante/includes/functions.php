<?php
declare(strict_types=1);

function e(mixed $value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $sent = $_POST['csrf_token'] ?? '';
    $saved = $_SESSION['csrf_token'] ?? '';

    if (!is_string($sent) || !is_string($saved) || $saved === '' || !hash_equals($saved, $sent)) {
        http_response_code(419);
        exit('La sesión del formulario expiró. Regrese e inténtelo nuevamente.');
    }
}

function money(float|string $amount): string
{
    return '$' . number_format((float)$amount, 2, '.', ',');
}

function role_label(string $role): string
{
    return [
        'Administrator' => 'Administrador',
        'Chef' => 'Chef',
        'Waiter' => 'Camarero',
        'Client' => 'Cliente',
    ][$role] ?? $role;
}

function status_label(string $status): string
{
    return [
        'Pending' => 'Pendiente',
        'Preparing' => 'En preparación',
        'Ready to serve' => 'Listo para servir',
        'Served' => 'Entregado',
    ][$status] ?? $status;
}

function status_badge(string $status): string
{
    return [
        'Pending' => 'warning',
        'Preparing' => 'info',
        'Ready to serve' => 'primary',
        'Served' => 'success',
    ][$status] ?? 'secondary';
}

function post_string(string $key, int $maxLength = 255): string
{
    $value = trim((string)($_POST[$key] ?? ''));
    $characters = preg_split('//u', $value, -1, PREG_SPLIT_NO_EMPTY);
    if ($characters === false) {
        return substr($value, 0, $maxLength);
    }
    return implode('', array_slice($characters, 0, $maxLength));
}
