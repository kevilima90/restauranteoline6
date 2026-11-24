<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_role('Administrator');

$editUser = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT id, username, email, role_id, is_active FROM users WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editUser = $stmt->fetch() ?: null;
}

$roles = $pdo->query('SELECT id, name FROM roles ORDER BY id')->fetchAll();
$users = $pdo->query(
    'SELECT u.id, u.username, u.email, u.is_active, u.created_at, r.name AS role
     FROM users u INNER JOIN roles r ON r.id = u.role_id
     ORDER BY u.is_active DESC, u.username'
)->fetchAll();

$pageTitle = 'Usuarios';
require __DIR__ . '/includes/header.php';
?>
<div class="mb-4">
    <p class="eyebrow mb-1">Administración</p>
    <h1 class="h2 fw-bold">Gestión de usuarios</h1>
    <p class="text-secondary mb-0">Cree cuentas y asigne los roles del sistema.</p>
</div>

<div class="content-card mb-4">
    <h2 class="h5 fw-bold"><?= $editUser ? 'Editar usuario' : 'Registrar usuario' ?></h2>
    <form method="post" action="user_save.php" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= e($editUser['id'] ?? '') ?>">
        <div class="col-md-4">
            <label for="username" class="form-label">Nombre de usuario</label>
            <input class="form-control" id="username" name="username" maxlength="50" required value="<?= e($editUser['username'] ?? '') ?>">
        </div>
        <div class="col-md-4">
            <label for="user_email" class="form-label">Correo electrónico</label>
            <input class="form-control" id="user_email" name="email" type="email" maxlength="100" required value="<?= e($editUser['email'] ?? '') ?>">
        </div>
        <div class="col-md-4">
            <label for="role_id" class="form-label">Rol</label>
            <select class="form-select" id="role_id" name="role_id" required>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= (int)$role['id'] ?>" <?= isset($editUser['role_id']) && (int)$editUser['role_id'] === (int)$role['id'] ? 'selected' : '' ?>>
                        <?= e(role_label($role['name'])) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label for="user_password" class="form-label">Contraseña <?= $editUser ? '(dejar vacía para conservarla)' : '' ?></label>
            <input class="form-control" id="user_password" name="password" type="password" minlength="8" <?= $editUser ? '' : 'required' ?> autocomplete="new-password">
            <div class="form-text">Mínimo 8 caracteres.</div>
        </div>
        <?php if ($editUser): ?>
        <div class="col-md-6 d-flex align-items-end">
            <label class="form-check-label mb-2">
                <input class="form-check-input me-2" type="checkbox" name="is_active" value="1" <?= (int)$editUser['is_active'] === 1 ? 'checked' : '' ?>>
                Cuenta activa
            </label>
        </div>
        <?php endif; ?>
        <div class="col-12 d-flex gap-2">
            <button class="btn btn-brand" type="submit"><?= $editUser ? 'Guardar cambios' : 'Crear usuario' ?></button>
            <?php if ($editUser): ?><a href="users.php" class="btn btn-outline-secondary">Cancelar</a><?php endif; ?>
        </div>
    </form>
</div>

<div class="content-card">
    <h2 class="h5 fw-bold mb-3">Usuarios registrados</h2>
    <div class="table-responsive">
        <table class="table align-middle app-table">
            <thead><tr><th>Usuario</th><th>Correo</th><th>Rol</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($users as $item): ?>
                <tr>
                    <td class="fw-semibold"><?= e($item['username']) ?></td>
                    <td><?= e($item['email']) ?></td>
                    <td><?= e(role_label($item['role'])) ?></td>
                    <td><span class="badge text-bg-<?= $item['is_active'] ? 'success' : 'secondary' ?>"><?= $item['is_active'] ? 'Activo' : 'Inactivo' ?></span></td>
                    <td>
                        <div class="d-flex gap-2">
                            <a class="btn btn-sm btn-outline-brand" href="users.php?edit=<?= (int)$item['id'] ?>">Editar</a>
                            <?php if ((int)$item['id'] !== (int)current_user()['id']): ?>
                                <form method="post" action="user_delete.php" data-confirm="¿Desea cambiar el estado de esta cuenta?">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                    <button class="btn btn-sm btn-outline-<?= $item['is_active'] ? 'danger' : 'success' ?>" type="submit">
                                        <?= $item['is_active'] ? 'Desactivar' : 'Activar' ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
