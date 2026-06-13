<?php
/**
 * Database connection — PDO factory with environment fallback.
 *
 * Reads DB_HOST, MARIADB_DATABASE, MARIADB_USER, MARIADB_PASSWORD
 * from environment variables. Falls back to Docker Compose defaults.
 *
 * @return PDO
 */
function getDB(): PDO
{
    $host     = getenv('DB_HOST') ?: 'db';
    $dbname   = getenv('MARIADB_DATABASE') ?: 'blogdb';
    $user     = getenv('MARIADB_USER') ?: 'bloguser';
    $password = getenv('MARIADB_PASSWORD') ?: 'blog_pass_2024';

    static $pdo = null;

    if ($pdo === null) {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $user,
            $password,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    }

    return $pdo;
}

/**
 * Ensure the users table supports mandatory password changes.
 *
 * This keeps existing Docker/Proxmox databases compatible after pulling a new
 * version of the application, without requiring a destructive DB reset.
 *
 * @return void
 */
function ensurePasswordChangeColumn(): void
{
    static $checked = false;

    if ($checked) {
        return;
    }

    $pdo = getDB();
    $pdo->exec('ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS requiere_cambio_password TINYINT(1) NOT NULL DEFAULT 0 AFTER password_hash');

    $checked = true;
}

/**
 * Fetch perfil data from the database.
 *
 * Queries the single-row perfil table and returns the row as an associative
 * array. Falls back to sensible defaults if the table is empty.
 *
 * @return array{nombre_completo: string, hero_descripcion: string, sobre_mi_texto: string, legajo: string, ubicacion: string, github_url: string, linkedin_url: string}
 */
function getPerfil(): array
{
    $pdo  = getDB();
    $stmt = $pdo->query('SELECT * FROM perfil LIMIT 1');
    $row  = $stmt->fetch();

    if ($row === false) {
        return [
            'nombre_completo'  => 'Nombre del Alumno',
            'hero_descripcion' => 'Descripción pendiente',
            'sobre_mi_texto'   => '',
            'legajo'           => '',
            'ubicacion'        => '',
            'github_url'       => '',
            'linkedin_url'     => '',
        ];
    }

    return $row;
}

/**
 * Return the application base path (no trailing slash).
 *
 * Reads APP_BASE_PATH from the environment. Defaults to an empty string
 * so the app still works when deployed at web root.
 *
 * Example: '/38249507' when APP_BASE_PATH=/38249507 is set in .env.
 *
 * @return string
 */
function app_base(): string
{
    static $base = null;

    if ($base === null) {
        $base = rtrim(getenv('APP_BASE_PATH') ?: '', '/');
    }

    return $base;
}

/**
 * Build an application URL from a root-relative path.
 *
 * Prepends the configured base path so URLs are correct whether the app
 * runs at web root or under a sub-path like /38249507.
 *
 * @param string $path Root-relative path, e.g. '/login' or '/assets/css/style.css'
 * @return string
 */
function app_url(string $path): string
{
    return app_base() . '/' . ltrim($path, '/');
}

/**
 * Redirect to an application URL and terminate execution.
 *
 * @param string $path Root-relative path, e.g. '/dashboard'
 * @return void
 */
function app_redirect(string $path): void
{
    header('Location: ' . app_url($path));
    exit;
}
