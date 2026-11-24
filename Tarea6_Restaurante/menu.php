
<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

// Buscar la imagen correspondiente al nombre del plato
function menu_normalize_name($name) {
    $name = strtolower(trim($name));
    $name = strtr($name, [
        'á'=>'a', 'é'=>'e', 'í'=>'i', 'ó'=>'o', 'ú'=>'u',
        'ñ'=>'n'
    ]);
    return preg_replace('/[^a-z0-9]/', '', $name);
}

function menu_get_image($dishName) {
    $folder = __DIR__ . '/assets/img/platos';

    if (!is_dir($folder)) {
        return null;
    }

    $files = scandir($folder);

    foreach ($files ?: [] as $file) {
        $fullPath = $folder . DIRECTORY_SEPARATOR . $file;

        if (!is_file($fullPath)) {
            continue;
        }

        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            continue;
        }

        $fileName = pathinfo($file, PATHINFO_FILENAME);

        if (menu_normalize_name($fileName) === menu_normalize_name($dishName)) {
            return 'assets/img/platos/' . rawurlencode($file);
        }
    }

    return null;
}

$isAdmin = current_user()['role'] === 'Administrator';
$editDish = null;

if ($isAdmin && isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM dishes WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editDish = $stmt->fetch() ?: null;
}

if ($isAdmin) {
    $dishes = $pdo->query(
        'SELECT * FROM dishes ORDER BY is_available DESC, name'
    )->fetchAll();
} else {
    $dishes = $pdo->query(
        'SELECT * FROM dishes WHERE is_available = 1 ORDER BY name'
    )->fetchAll();
}

$pageTitle = 'Menú del restaurante';
require __DIR__ . '/includes/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <p class="eyebrow mb-1">Nuestra carta</p>
        <h1 class="h2 fw-bold mb-0">Menú del restaurante</h1>
    </div>

    <?php if ($isAdmin): ?>
        <a href="menu.php#dish-form" class="btn btn-brand">+ Agregar plato</a>
    <?php elseif (in_array(current_user()['role'], ['Client', 'Waiter'], true)): ?>
        <a href="order_create.php" class="btn btn-brand">Realizar pedido</a>
    <?php endif; ?>
</div>

<?php if ($isAdmin): ?>
<div class="content-card mb-4" id="dish-form">
    <h2 class="h5 fw-bold">
        <?= $editDish ? 'Editar plato' : 'Nuevo plato' ?>
    </h2>

    <form method="post" action="dish_save.php" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= e($editDish['id'] ?? '') ?>">

        <div class="col-md-4">
            <label class="form-label" for="name">Nombre</label>
            <input class="form-control" id="name" name="name"
                maxlength="50" required
                value="<?= e($editDish['name'] ?? '') ?>">
        </div>

        <div class="col-md-5">
            <label class="form-label" for="description">Descripción</label>
            <input class="form-control" id="description" name="description"
                maxlength="255"
                value="<?= e($editDish['description'] ?? '') ?>">
        </div>

        <div class="col-md-3">
            <label class="form-label" for="price">Precio (USD)</label>
            <input class="form-control" id="price" name="price"
                type="number" min="0.01" step="0.01" required
                value="<?= e($editDish['price'] ?? '') ?>">
        </div>

        <?php if ($editDish): ?>
            <div class="col-12">
                <label class="form-check-label">
                    <input class="form-check-input me-2" type="checkbox"
                        name="is_available" value="1"
                        <?= (int)$editDish['is_available'] === 1 ? 'checked' : '' ?>>
                    Plato disponible
                </label>
            </div>
        <?php endif; ?>

        <div class="col-12 d-flex gap-2">
            <button class="btn btn-brand" type="submit">
                <?= $editDish ? 'Guardar cambios' : 'Crear plato' ?>
            </button>

            <?php if ($editDish): ?>
                <a class="btn btn-outline-secondary" href="menu.php">Cancelar</a>
            <?php endif; ?>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="row g-3">

    <?php if (!$dishes): ?>
        <div class="col-12">
            <div class="empty-state">Todavía no hay platos registrados.</div>
        </div>
    <?php endif; ?>

    <?php foreach ($dishes as $dish): ?>
        <?php $imagePath = menu_get_image($dish['name']); ?>

        <div class="col-sm-6 col-xl-4">
            <article class="dish-card h-100">

                <div class="dish-art"
                    style="height:220px; overflow:hidden; background:#f4f1ed; display:flex; align-items:center; justify-content:center;">

                    <?php if ($imagePath): ?>
                        <img
                            src="<?= e($imagePath) ?>"
                            alt="<?= e($dish['name']) ?>"
                            loading="lazy"
                            style="width:100%; height:100%; object-fit:cover; display:block;">
                    <?php else: ?>
                        <span style="font-size:3rem;">🍽️</span>
                    <?php endif; ?>

                </div>

                <div class="p-3">

                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <h2 class="h5 fw-bold mb-1">
                            <?= e($dish['name']) ?>
                        </h2>

                        <span class="price-tag">
                            <?= e(money($dish['price'])) ?>
                        </span>
                    </div>

                    <p class="text-secondary small mb-3">
                        <?= e($dish['description'] ?: 'Preparado por nuestro equipo de cocina.') ?>
                    </p>

                    <?php if (!$dish['is_available']): ?>
                        <span class="badge text-bg-secondary">No disponible</span>

                    <?php elseif (in_array(current_user()['role'], ['Client', 'Waiter'], true)): ?>
                        <a href="order_create.php?dish_id=<?= (int)$dish['id'] ?>"
                            class="btn btn-sm btn-brand">
                            Pedir este plato
                        </a>
                    <?php endif; ?>

                    <?php if ($isAdmin): ?>
                        <div class="d-flex gap-2 mt-2">

                            <a href="menu.php?edit=<?= (int)$dish['id'] ?>#dish-form"
                                class="btn btn-sm btn-outline-brand">
                                Editar
                            </a>

                            <?php if ($dish['is_available']): ?>
                                <form method="post" action="dish_delete.php"
                                    class="m-0"
                                    data-confirm="¿Desea retirar este plato del menú?">

                                    <input type="hidden" name="csrf_token"
                                        value="<?= e(csrf_token()) ?>">

                                    <input type="hidden" name="id"
                                        value="<?= (int)$dish['id'] ?>">

                                    <button class="btn btn-sm btn-outline-danger" type="submit">
                                        Retirar
                                    </button>
                                </form>
                            <?php endif; ?>

                        </div>
                    <?php endif; ?>

                </div>
            </article>
        </div>

    <?php endforeach; ?>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
