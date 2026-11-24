<?php
declare(strict_types=1);

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): void
{
    if (current_user() === null) {
        set_flash('warning', 'Inicie sesión para continuar.');
        redirect('login.php');
    }
}

function require_role(string ...$roles): void
{
    require_login();

    $currentRole = current_user()['role'] ?? '';
    if (!in_array($currentRole, $roles, true)) {
        http_response_code(403);
        exit('Acceso denegado: su rol no tiene permiso para realizar esta acción.');
    }
}

function login_user(array $user): void
{
    session_regenerate_id(true);

    $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'role_id' => (int)$user['role_id'],
        'role' => $user['role'],
    ];
}

function logout_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
}
