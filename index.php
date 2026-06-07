<?php
require_once __DIR__ . '/db.php';

// Fetch publications and perfil from database
$perfil = [
    'nombre_completo'  => 'Nombre del Alumno',
    'hero_descripcion' => 'Descripción pendiente',
    'sobre_mi_texto'   => '',
    'legajo'           => '',
    'ubicacion'        => '',
    'github_url'       => '',
    'linkedin_url'     => '',
];

try {
    $pdo            = getDB();
    $stmt           = $pdo->query('SELECT id, titulo, contenido, categoria, fecha_creacion FROM publicaciones ORDER BY fecha_creacion DESC');
    $publicaciones  = $stmt->fetchAll();

    $perfil = getPerfil();
} catch (PDOException $e) {
    $publicaciones = [];
}
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rodrigo Zurita — Blog Personal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<!-- ===== Navigation ===== -->
<nav class="navbar" role="navigation" aria-label="Navegación principal">
    <div class="container nav-container">
        <a href="/" class="nav-logo" aria-label="Ir al inicio">RZ</a>
        <ul class="nav-links">
            <li><a href="/" class="active">Inicio</a></li>
            <li><a href="#about">Sobre mí</a></li>
            <li><a href="#publicaciones">Publicaciones</a></li>

        </ul>
    </div>
</nav>

<!-- ===== Hero ===== -->
<section class="hero">
    <div class="container hero-container">
        <h1 class="hero-name"><?= htmlspecialchars($perfil['nombre_completo'] ?? 'Nombre del Alumno', ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="hero-subtitle"><?= htmlspecialchars($perfil['hero_descripcion'] ?? 'Descripción pendiente', ENT_QUOTES, 'UTF-8') ?></p>
        <div class="hero-actions">
            <a href="#about" class="btn btn-primary">Sobre mí</a>
            <a href="#publicaciones" class="btn btn-secondary">Ver publicaciones</a>
        </div>
    </div>

    <!-- Hero bottom bar: social links + info -->
    <div class="hero-bottom-bar">
        <div class="hero-social">
            <?php if (!empty($perfil['github_url'])): ?>
            <a href="<?= htmlspecialchars($perfil['github_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" aria-label="GitHub">
                <svg viewBox="0 0 24 24"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>
            </a>
            <?php endif; ?>
            <?php if (!empty($perfil['linkedin_url'])): ?>
            <a href="<?= htmlspecialchars($perfil['linkedin_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" aria-label="LinkedIn">
                <svg viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
            </a>
            <?php endif; ?>
        </div>
        <div class="hero-info">
            <?php if (!empty($perfil['legajo'])): ?>
            <span class="hero-info-item">
                <svg viewBox="0 0 24 24"><path d="M4 4h16v2H4V4zm0 4h16v2H4V8zm0 4h16v2H4v-2zm0 4h10v2H4v-2z"/></svg>
                Legajo: <?= htmlspecialchars($perfil['legajo'], ENT_QUOTES, 'UTF-8') ?>
            </span>
            <?php endif; ?>
            <?php if (!empty($perfil['ubicacion'])): ?>
            <span class="hero-info-item">
                <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                <?= htmlspecialchars($perfil['ubicacion'], ENT_QUOTES, 'UTF-8') ?>
            </span>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ===== About ===== -->
<section id="about" class="about section-pad">
    <div class="container">
        <div class="about-grid">
            <div class="about-image-col">
                <?php if (file_exists(__DIR__ . '/assets/img/foto_perfil.png')): ?>
                    <img src="/assets/img/foto_perfil.png"
                         alt="Rodrigo Zurita"
                         class="about-photo"
                         loading="lazy">
                <?php else: ?>
                    <div class="about-photo-placeholder" role="img" aria-label="Foto de perfil no disponible">
                        <span>RZ</span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="about-content-col">
                <h2 class="section-headline">Sobre mí</h2>
                <p class="about-text"><?= htmlspecialchars($perfil['sobre_mi_texto'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>
    </div>
</section>

<!-- ===== Publications ===== -->
<section id="publicaciones" class="publications section-pad">
    <div class="container">
        <h2 class="section-headline">Publicaciones</h2>

        <?php if (empty($publicaciones)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📝</div>
                <p class="empty-state-text">Todavía no hay publicaciones.</p>
                <p class="empty-state-sub">Las publicaciones aparecerán aquí una vez que sean cargadas desde el panel de administración.</p>
            </div>
        <?php else: ?>
            <div class="pub-grid">
                <?php foreach ($publicaciones as $pub): ?>
                    <article class="pub-card">
                        <div class="pub-card-frame" aria-hidden="true"></div>
                        <div class="pub-card-body">
                            <div class="pub-meta">
                                <?php if (!empty($pub['categoria'])): ?>
                                    <span class="pub-category"><?= htmlspecialchars($pub['categoria'], ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endif; ?>
                                <time class="pub-date" datetime="<?= htmlspecialchars($pub['fecha_creacion'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars(date('d/m/Y', strtotime($pub['fecha_creacion'])), ENT_QUOTES, 'UTF-8') ?>
                                </time>
                            </div>
                            <h3 class="pub-title"><?= htmlspecialchars($pub['titulo'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p class="pub-excerpt"><?= htmlspecialchars($pub['contenido'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ===== Footer ===== -->
<footer class="footer">
    <div class="container">
        <p class="footer-text">&copy; <?= date('Y') ?> Rodrigo Zurita — Blog Personal · Ingeniería en Sistemas de Información</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js" defer></script>
<script src="/assets/js/main.js" defer></script>

</body>
</html>
