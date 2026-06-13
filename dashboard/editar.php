<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../db.php';

requiere_autenticacion();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$id      = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error   = '';
$titulo  = '';
$contenido = '';
$categoria = '';

// Handle POST (update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    // CSRF
    if (!isset($_POST['csrf_token'], $_SESSION['csrf_token'])
        || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        app_redirect('/dashboard?error=csrf');
    }

    $titulo    = trim($_POST['titulo'] ?? '');
    $contenido = trim($_POST['contenido'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');

    $errors = [];
    if ($titulo === '')            $errors[] = 'titulo';
    if (mb_strlen($titulo) > 150)  $errors[] = 'titulo_max';
    if ($contenido === '')         $errors[] = 'contenido';
    if (mb_strlen($categoria) > 80) $errors[] = 'categoria_max';

    if (!empty($errors)) {
        app_redirect('/dashboard?error=' . implode(',', $errors));
    }

    try {
        $pdo  = getDB();
        $stmt = $pdo->prepare('UPDATE publicaciones SET titulo = ?, contenido = ?, categoria = ? WHERE id = ?');
        $stmt->execute([$titulo, $contenido, $categoria !== '' ? $categoria : null, $id]);

        if ($stmt->rowCount() === 0) {
            app_redirect('/dashboard?error=notfound');
        }

        app_redirect('/dashboard?success=editado');
    } catch (PDOException $e) {
        app_redirect('/dashboard?error=db');
    }
}

// GET: pre-fill form
if ($id <= 0) {
    app_redirect('/dashboard');
}

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare('SELECT id, titulo, contenido, categoria FROM publicaciones WHERE id = ?');
    $stmt->execute([$id]);
    $pub = $stmt->fetch();

    if (!$pub) {
        app_redirect('/dashboard?error=notfound');
    }

    $titulo    = $pub['titulo'];
    $contenido = $pub['contenido'];
    $categoria = $pub['categoria'] ?? '';
} catch (PDOException $e) {
    app_redirect('/dashboard?error=db');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar publicación — Blog Personal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= app_url('/assets/style.php') ?>">
</head>
<body class="dashboard-body">

<nav class="navbar dashboard-nav">
    <div class="container nav-container">
        <a href="<?= app_url('/dashboard') ?>" class="nav-logo" aria-label="Volver al panel">RZ</a>
        <div class="nav-links">
            <a href="<?= app_url('/') ?>" class="nav-link-icon" target="_blank">Ver sitio</a>
            <a href="<?= app_url('/logout.php') ?>" class="nav-link-icon nav-logout">Cerrar sesión</a>
        </div>
    </div>
</nav>

<main class="dashboard-main container">

    <a href="<?= app_url('/dashboard') ?>" class="btn btn-secondary btn-sm" style="margin-bottom: 1rem;">&larr; Volver al panel</a>
    <h1 class="dashboard-headline">Editar publicación</h1>

    <section class="dashboard-section">
        <form method="POST" action="<?= app_url('/dashboard/editar.php') ?>" class="pub-form" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="id" value="<?= $id ?>">

            <div class="form-group">
                <label for="titulo" class="form-label">Título *</label>
                <input type="text" id="titulo" name="titulo"
                       class="form-input"
                       maxlength="150"
                       required
                       value="<?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="categoria" class="form-label">Categoría</label>
                <input type="text" id="categoria" name="categoria"
                       class="form-input"
                       maxlength="80"
                       value="<?= htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="contenido" class="form-label">Contenido *</label>
                <textarea id="contenido" name="contenido"
                          class="form-textarea"
                          rows="8"
                          required><?= htmlspecialchars($contenido, ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                <a href="<?= app_url('/dashboard') ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </section>

</main>

</body>
</html>
