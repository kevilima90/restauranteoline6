<?php
require_once 'C:/xampp/htdocs/Tarea6_Restaurante/includes/bootstrap.php';
if (current_user()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email = trim((string)($_POST['email'] ?? ''));
    $passwordInput = (string)($_POST['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $passwordInput === '') {
        $error = 'Ingrese un correo electrónico válido y su contraseña.';
    } else {
        $statement = $pdo->prepare(
            'SELECT u.id, u.username, u.email, u.password, u.role_id, r.name AS role
             FROM users u
             INNER JOIN roles r ON r.id = u.role_id
             WHERE u.email = :email AND u.is_active = 1
             LIMIT 1'
        );

        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        if ($user && password_verify($passwordInput, $user['password'])) {
            login_user($user);
            set_flash('success', 'Bienvenido/a al sistema.');
            redirect('dashboard.php');
        }

        $error = 'Las credenciales son incorrectas o la cuenta está desactivada.';
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión | Restaurante Online</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">

    <style>
        .login-layout {
            width: 100%;
            max-width: 1050px;
        }

        .login-card {
            width: 100%;
            max-width: none;
            margin: 0;
        }

        .demo-card {
            background: #fff;
            border-radius: 18px;
            padding: 28px;
            height: 100%;
            box-shadow: 0 8px 30px rgba(0,0,0,.10);
        }

        .demo-title {
            font-size: 21px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .demo-subtitle {
            color: #777;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .demo-account {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            padding: 14px 0;
            border-bottom: 1px solid #eeeeee;
        }

        .demo-account:last-child {
            border-bottom: none;
        }

        .demo-account strong {
            display: block;
            color: #873c30;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .demo-account small {
            display: block;
            font-size: 12px;
            color: #555;
            overflow-wrap: anywhere;
        }

        .use-demo-account {
            white-space: nowrap;
            font-size: 12px;
        }

        @media (max-width: 576px) {
            .demo-card {
                padding: 20px;
            }
        }
    </style>
</head>

<body class="login-page">

<div class="container min-vh-100 d-flex align-items-center justify-content-center py-4">

    <div class="row login-layout align-items-stretch g-4">

        <!-- FORMULARIO DE INICIO DE SESIÓN -->
        <div class="col-lg-6">

            <div class="login-card shadow-lg h-100">

                <div class="text-center mb-4">

                    <div class="brand-mark mx-auto mb-3">S</div>

                    <p class="eyebrow mb-1">Sazón &amp; Servicio</p>

                    <h1 class="h3 fw-bold">Restaurante Online</h1>

                    <p class="text-secondary mb-0">
                        Ingrese con su cuenta para continuar
                    </p>

                </div>

                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger">
                        <?= e($error) ?>
                    </div>
                <?php endif; ?>

                <?php $flash = get_flash(); ?>

                <?php if ($flash): ?>
                    <div class="alert alert-<?= e($flash['type']) ?>">
                        <?= e($flash['message']) ?>
                    </div>
                <?php endif; ?>

                <form method="post" novalidate>

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e(csrf_token()) ?>"
                    >

                    <div class="mb-3">

                        <label for="email" class="form-label">
                            Correo electrónico
                        </label>

                        <input
                            class="form-control form-control-lg"
                            id="email"
                            name="email"
                            type="email"
                            required
                            autocomplete="username"
                            value="<?= e($_POST['email'] ?? '') ?>"
                        >

                    </div>

                    <div class="mb-4">

                        <label for="password" class="form-label">
                            Contraseña
                        </label>

                        <input
                            class="form-control form-control-lg"
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                        >

                    </div>

                    <button class="btn btn-brand btn-lg w-100" type="submit">
                        Ingresar
                    </button>

                </form>

                <p class="text-center text-secondary small mt-4 mb-0">
                    Acceso exclusivo para usuarios registrados.
                </p>

            </div>

        </div>

        <!-- CUENTAS DE DEMOSTRACIÓN -->
        <div class="col-lg-6">

            <div class="demo-card">

                <div class="text-center">

                    <div class="demo-title">
                        Cuentas de acceso
                    </div>

                    <p class="demo-subtitle">
                        Credenciales de demostración para el docente
                    </p>

                </div>

                <!-- ADMINISTRADOR -->
                <div class="demo-account">

                    <div>
                        <strong>Administrador</strong>
                        <small>Correo: admin@restaurante.local</small>
                        <small>Contraseña: Admin123!</small>
                    </div>

                    <button
                        type="button"
                        class="btn btn-outline-danger use-demo-account"
                        data-email="admin@restaurante.local"
                        data-password="Admin123!"
                    >
                        Usar cuenta
                    </button>

                </div>

                <!-- CHEF -->
                <div class="demo-account">

                    <div>
                        <strong>Chef</strong>
                        <small>Correo: chef@restaurante.local</small>
                        <small>Contraseña: Chef123!</small>
                    </div>

                    <button
                        type="button"
                        class="btn btn-outline-danger use-demo-account"
                        data-email="chef@restaurante.local"
                        data-password="Chef123!"
                    >
                        Usar cuenta
                    </button>

                </div>

                <!-- CAMARERO -->
                <div class="demo-account">

                    <div>
                        <strong>Camarero</strong>
                        <small>Correo: camarero@restaurante.local</small>
                        <small>Contraseña: Camarero123!</small>
                    </div>

                    <button
                        type="button"
                        class="btn btn-outline-danger use-demo-account"
                        data-email="camarero@restaurante.local"
                        data-password="Camarero123!"
                    >
                        Usar cuenta
                    </button>

                </div>

                <!-- CLIENTE -->
                <div class="demo-account">

                    <div>
                        <strong>Cliente</strong>
                        <small>Correo: cliente@restaurante.local</small>
                        <small>Contraseña: Cliente123!</small>
                    </div>

                    <button
                        type="button"
                        class="btn btn-outline-danger use-demo-account"
                        data-email="cliente@restaurante.local"
                        data-password="Cliente123!"
                    >
                        Usar cuenta
                    </button>

                </div>

                <div class="alert alert-light border small mt-3 mb-0">
                    Seleccione una cuenta para completar automáticamente
                    los datos de acceso.
                </div>

            </div>

        </div>

    </div>

</div>

<script>
document.querySelectorAll('.use-demo-account').forEach(button => {

    button.addEventListener('click', function () {

        document.getElementById('email').value =
            this.dataset.email;

        document.getElementById('password').value =
            this.dataset.password;

        document.getElementById('password').focus();

    });

});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>