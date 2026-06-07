<?php
/**
 * Admin user seeder — run once after docker-compose up.
 *
 * Usage:
 *   docker exec -it blog_app_1 php /var/www/html/db/init-admin.php
 *
 * Or access via browser and DELETE the file after use:
 *   http://localhost:8080/db/init-admin.php
 *
 * Idempotent — safe to run multiple times.
 */

$host    = getenv('DB_HOST') ?: 'db';
$dbname  = getenv('MARIADB_DATABASE') ?: 'blogdb';
$user    = getenv('MARIADB_USER') ?: 'bloguser';
$pass    = getenv('MARIADB_PASSWORD') ?: 'blog_pass_2024';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    $pdo->exec('ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS requiere_cambio_password TINYINT(1) NOT NULL DEFAULT 0 AFTER password_hash');

    $check = $pdo->prepare('SELECT id, password_hash, requiere_cambio_password FROM usuarios WHERE usuario = ?');
    $check->execute(['admin']);
    $admin = $check->fetch();

    if ($admin) {
        if (password_verify('admin123', $admin['password_hash']) && !(bool)$admin['requiere_cambio_password']) {
            $forceChange = $pdo->prepare('UPDATE usuarios SET requiere_cambio_password = 1 WHERE id = ?');
            $forceChange->execute([$admin['id']]);
            echo "✅ Admin user already exists. Mandatory password change enabled.\n";
            exit(0);
        }

        echo "✅ Admin user already exists. Nothing to do.\n";
        exit(0);
    }

    $hash   = password_hash('admin123', PASSWORD_DEFAULT);
    $insert = $pdo->prepare('INSERT INTO usuarios (usuario, password_hash, requiere_cambio_password) VALUES (?, ?, ?)');
    $insert->execute(['admin', $hash, 1]);

    echo "✅ Admin user created successfully.\n";
    echo "   Username: admin\n";
    echo "   Password: admin123\n";
    echo "   ⚠️  The first login will require changing this password.\n";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
