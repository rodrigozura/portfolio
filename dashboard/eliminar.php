<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../db.php';

requiere_autenticacion();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pub = null;

// Handle POST (confirm delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if (!isset($_POST['csrf_token'], $_SESSION['csrf_token'])
        || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        header('Location: /dashboard?error=csrf');
        exit;
    }

    if ($id <= 0) {
        header('Location: /dashboard?error=notfound');
        exit;
    }

    try {
        $pdo  = getDB();
        $stmt = $pdo->prepare('DELETE FROM publicaciones WHERE id = ?');
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            header('Location: /dashboard?error=notfound');
            exit;
        }

        header('Location: /dashboard?success=eliminado');
        exit;
    } catch (PDOException $e) {
        header('Location: /dashboard?error=db');
        exit;
    }
}

// GET: show confirmation
if ($id <= 0) {
    header('Location: /dashboard');
    exit;
}

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare('SELECT id, titulo FROM publicaciones WHERE id = ?');
    $stmt->execute([$id]);
    $pub = $stmt->fetch();

    if (!$pub) {
        header('Location: /dashboard?error=notfound');
        exit;
    }
} catch (PDOException $e) {
    header('Location: /dashboard?error=db');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar publicación — Blog Personal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="dashboard-body">

<nav class="navbar dashboard-nav">
    <div class="container nav-container">
        <a href="/dashboard" class="nav-logo" aria-label="Volver al panel">RZ</a>
        <div class="nav-links">
            <a href="/" class="nav-link-icon" target="_blank">Ver sitio</a>
            <a href="/logout.php" class="nav-link-icon nav-logout">Cerrar sesión</a>
        </div>
    </div>
</nav>

<main class="dashboard-main container">

    <a href="/dashboard" class="btn btn-secondary btn-sm" style="margin-bottom: 1rem;">&larr; Volver al panel</a>
    <h1 class="dashboard-headline">Eliminar publicación</h1>

    <section class="dashboard-section">
        <div class="delete-confirm">
            <p class="delete-warning">
                ¿Estás seguro de que deseas eliminar la publicación
                <strong>"<?= htmlspecialchars($pub['titulo'], ENT_QUOTES, 'UTF-8') ?>"</strong>?
            </p>
            <p class="delete-hint">Esta acción no se puede deshacer.</p>

            <form method="POST" action="/dashboard/eliminar.php" class="delete-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="id" value="<?= $id ?>">

                <div class="form-actions">
                    <button type="submit" class="btn btn-danger">Sí, eliminar</button>
                    <a href="/dashboard" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </section>

</main>

</body>
</html>
