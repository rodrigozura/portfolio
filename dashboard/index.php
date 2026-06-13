<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../db.php';

requiere_autenticacion();

// Fetch all publications
try {
    $pdo           = getDB();
    $stmt          = $pdo->query('SELECT id, titulo, contenido, categoria, fecha_creacion FROM publicaciones ORDER BY fecha_creacion DESC');
    $publicaciones = $stmt->fetchAll();
} catch (PDOException $e) {
    $publicaciones = [];
}

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Determine status message from GET params
$success = $_GET['success'] ?? '';
$error   = $_GET['error'] ?? '';

$successMessages = [
    'creado'  => 'Publicación creada correctamente.',
    'editado' => 'Publicación actualizada correctamente.',
    'eliminado' => 'Publicación eliminada correctamente.',
    'password_changed' => 'Contraseña actualizada correctamente.',
];

$errorMessages = [
    'csrf'         => 'Error de validación. Intente nuevamente.',
    'db'           => 'Error de base de datos. Intente nuevamente.',
    'titulo'       => 'El título es obligatorio.',
    'titulo_max'   => 'El título no puede superar los 150 caracteres.',
    'contenido'    => 'El contenido es obligatorio.',
    'categoria_max' => 'La categoría no puede superar los 80 caracteres.',
    'notfound'     => 'Publicación no encontrada.',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel — Blog Personal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= app_url('/assets/css/style.css') ?>">
</head>
<body class="dashboard-body">

<!-- ===== Dashboard Nav ===== -->
<nav class="navbar dashboard-nav">
    <div class="container nav-container">
        <a href="<?= app_url('/') ?>" class="nav-logo" aria-label="Ir al inicio">RZ</a>
        <div class="nav-links">
            <span class="nav-greeting">Hola, <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Admin', ENT_QUOTES, 'UTF-8') ?></span>
            <a href="<?= app_url('/dashboard/perfil.php') ?>" class="nav-link-icon">Perfil</a>
            <a href="<?= app_url('/') ?>" class="nav-link-icon" target="_blank">Ver sitio</a>
            <a href="<?= app_url('/logout.php') ?>" class="nav-link-icon nav-logout">Cerrar sesión</a>
        </div>
    </div>
</nav>

<main class="dashboard-main container">

    <h1 class="dashboard-headline">Panel de administración</h1>

    <!-- ===== Status Messages ===== -->
    <?php if ($success !== '' && isset($successMessages[$success])): ?>
        <div class="alert alert-success" role="alert">
            <?= htmlspecialchars($successMessages[$success], ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php elseif ($error !== ''): ?>
        <?php
        $errorKeys = explode(',', $error);
        foreach ($errorKeys as $ek):
            if (isset($errorMessages[$ek])):
        ?>
            <div class="alert alert-error" role="alert">
                <?= htmlspecialchars($errorMessages[$ek], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php
            endif;
        endforeach;
        ?>
    <?php endif; ?>

    <!-- ===== Create Form ===== -->
    <section class="dashboard-section">
        <h2 class="section-headline">Nueva publicación</h2>
        <form method="POST" action="<?= app_url('/guardar_publicacion.php') ?>" class="pub-form" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label for="titulo" class="form-label">Título *</label>
                <input type="text" id="titulo" name="titulo"
                       class="form-input"
                       maxlength="150"
                       required
                       placeholder="Título de la publicación">
            </div>

            <div class="form-group">
                <label for="categoria" class="form-label">Categoría</label>
                <input type="text" id="categoria" name="categoria"
                       class="form-input"
                       maxlength="80"
                       placeholder="Ej: Tecnología, Académico">
            </div>

            <div class="form-group">
                <label for="contenido" class="form-label">Contenido *</label>
                <textarea id="contenido" name="contenido"
                          class="form-textarea"
                          rows="6"
                          required
                          placeholder="Escribe el contenido aquí..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Publicar</button>
        </form>
    </section>

    <!-- ===== Publication List ===== -->
    <section class="dashboard-section">
        <h2 class="section-headline">Publicaciones existentes</h2>

        <?php if (empty($publicaciones)): ?>
            <div class="empty-state">
                <p class="empty-state-text">No hay publicaciones todavía.</p>
                <p class="empty-state-sub">Usa el formulario de arriba para crear tu primera publicación.</p>
            </div>
        <?php else: ?>
            <div class="pub-list">
                <?php foreach ($publicaciones as $pub): ?>
                    <div class="pub-list-item">
                        <div class="pub-list-info">
                            <h3 class="pub-list-title"><?= htmlspecialchars($pub['titulo'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <div class="pub-list-meta">
                                <?php if (!empty($pub['categoria'])): ?>
                                    <span class="pub-category"><?= htmlspecialchars($pub['categoria'], ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endif; ?>
                                <time class="pub-date"><?= htmlspecialchars(date('d/m/Y', strtotime($pub['fecha_creacion'])), ENT_QUOTES, 'UTF-8') ?></time>
                            </div>
                        </div>
                        <div class="pub-list-actions">
                            <a href="<?= app_url('/dashboard/editar.php?id=' . (int)$pub['id']) ?>" class="btn btn-secondary btn-sm">Editar</a>
                            <a href="<?= app_url('/dashboard/eliminar.php?id=' . (int)$pub['id']) ?>" class="btn btn-danger btn-sm">Eliminar</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

</main>

</body>
</html>
