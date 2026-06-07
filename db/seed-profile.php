<?php
/**
 * Profile seeder — run once after docker-compose up.
 *
 * Usage:
 *   docker exec -it blog_app_1 php /var/www/html/db/seed-profile.php
 *
 * Or access via browser and DELETE the file after use:
 *   http://localhost:8080/db/seed-profile.php
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

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS perfil (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre_completo VARCHAR(150) NOT NULL,
            hero_descripcion TEXT NOT NULL,
            sobre_mi_texto TEXT NOT NULL,
            legajo VARCHAR(20) DEFAULT \'\',
            ubicacion VARCHAR(100) DEFAULT \'\',
            github_url VARCHAR(255) DEFAULT \'\',
            linkedin_url VARCHAR(255) DEFAULT \'\',
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );

    $check = $pdo->query('SELECT id FROM perfil LIMIT 1');

    if ($check->fetch()) {
        echo "✅ Perfil already has data. Nothing to do.\n";
        exit(0);
    }

    $stmt = $pdo->prepare(
        'INSERT INTO perfil (nombre_completo, hero_descripcion, sobre_mi_texto, legajo, ubicacion, github_url, linkedin_url)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $sobreMiTexto = <<<'TEXT'
Soy Rodrigo Zurita, estudiante de Ingeniería en Sistemas de Información y desarrollador de software con interés en crear soluciones tecnológicas que combinen ingeniería, datos, inteligencia artificial y negocio.

Actualmente trabajo en el desarrollo de sistemas y soluciones digitales, con un enfoque cada vez más orientado a inteligencia artificial aplicada. Mi experiencia comenzó vinculada al desarrollo web, pero con el tiempo fui ampliando mi perfil hacia el desarrollo full stack, la integración de servicios, la automatización de procesos y la construcción de soluciones inteligentes basadas en modelos de IA, flujos de trabajo asistidos y sistemas capaces de colaborar con usuarios y equipos técnicos.

Me interesa especialmente el punto donde la tecnología deja de ser solo código y empieza a resolver problemas reales: optimizar procesos, mejorar la toma de decisiones, reducir tareas repetitivas y transformar ideas en productos funcionales. Por eso disfruto trabajar en proyectos donde se combinan arquitectura de software, análisis de datos, experiencia de usuario e inteligencia artificial.

A nivel profesional, busco seguir creciendo como un perfil técnico integral, capaz de entender tanto la lógica del negocio como la implementación técnica de una solución. Me motiva aprender constantemente, investigar nuevas herramientas y aplicar ese conocimiento en proyectos concretos, especialmente en áreas como sistemas inteligentes, automatización, productos digitales, análisis de información y arquitectura de aplicaciones.

Fuera del trabajo y la universidad, disfruto de la tecnología, el ciclismo, los espacios de concentración, el aprendizaje autodidacta y los proyectos personales. Me gusta explorar nuevas ideas, organizar procesos, mejorar mis hábitos y mantener una mirada curiosa sobre cómo la innovación puede aplicarse en la vida diaria y en el mundo profesional.

En resumen, me considero una persona analítica, curiosa y orientada a construir. Mi objetivo es seguir desarrollándome como profesional en tecnología, aportando soluciones útiles, bien pensadas y alineadas a necesidades reales.
TEXT;

    $stmt->execute([
        'Sergio Rodrigo Zurita',
        'AI Engineer, Full Stack Developer, Estudiante de Ingeniería en Sistemas de Información',
        $sobreMiTexto,
        'XXXXX',
        'XXXX XXXX, XXXX',
        'https://github.com/XXXX',
        'https://www.linkedin.com/in/XXXX/',
    ]);

    echo "✅ Perfil seeded successfully.\n";
    echo "   Nombre: Sergio Rodrigo Zurita\n";
    echo "   Legajo: XXXXX\n";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
