CREATE TABLE IF NOT EXISTS publicaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    contenido TEXT NOT NULL,
    categoria VARCHAR(80),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS perfil (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(150) NOT NULL,
    hero_descripcion TEXT NOT NULL,
    sobre_mi_texto TEXT NOT NULL,
    legajo VARCHAR(20) DEFAULT '',
    ubicacion VARCHAR(100) DEFAULT '',
    github_url VARCHAR(255) DEFAULT '',
    linkedin_url VARCHAR(255) DEFAULT '',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    requiere_cambio_password TINYINT(1) NOT NULL DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed publications
INSERT INTO publicaciones (titulo, contenido, categoria) VALUES
('Presentación del Blog Personal', 'Este blog fue creado como parte del Trabajo Práctico Final, con el objetivo de implementar una aplicación web simple desplegada sobre una infraestructura de contenedores.', 'Académico'),
('Tecnologías utilizadas', 'El sistema fue desarrollado utilizando Apache, PHP y MariaDB. La aplicación se ejecuta en un contenedor independiente y se conecta a una base de datos alojada en otro contenedor.', 'Tecnología'),
('Implementación en Proxmox', 'La solución fue desplegada en Proxmox, respetando la separación entre el contenedor de aplicación y el contenedor de base de datos.', 'Infraestructura');

-- Seed profile data
INSERT INTO perfil (nombre_completo, hero_descripcion, sobre_mi_texto, legajo, ubicacion, github_url, linkedin_url)
SELECT
    'Sergio Rodrigo Zurita',
    'AI Engineer, Full Stack Developer, Estudiante de Ingeniería en Sistemas de Información',
    'Soy Rodrigo Zurita, estudiante de Ingeniería en Sistemas de Información y desarrollador de software con interés en crear soluciones tecnológicas que combinen ingeniería, datos, inteligencia artificial y negocio.

Actualmente trabajo en el desarrollo de sistemas y soluciones digitales, con un enfoque cada vez más orientado a inteligencia artificial aplicada. Mi experiencia comenzó vinculada al desarrollo web, pero con el tiempo fui ampliando mi perfil hacia el desarrollo full stack, la integración de servicios, la automatización de procesos y la construcción de soluciones inteligentes basadas en modelos de IA, flujos de trabajo asistidos y sistemas capaces de colaborar con usuarios y equipos técnicos.

Me interesa especialmente el punto donde la tecnología deja de ser solo código y empieza a resolver problemas reales: optimizar procesos, mejorar la toma de decisiones, reducir tareas repetitivas y transformar ideas en productos funcionales. Por eso disfruto trabajar en proyectos donde se combinan arquitectura de software, análisis de datos, experiencia de usuario e inteligencia artificial.

A nivel profesional, busco seguir creciendo como un perfil técnico integral, capaz de entender tanto la lógica del negocio como la implementación técnica de una solución. Me motiva aprender constantemente, investigar nuevas herramientas y aplicar ese conocimiento en proyectos concretos, especialmente en áreas como sistemas inteligentes, automatización, productos digitales, análisis de información y arquitectura de aplicaciones.

Fuera del trabajo y la universidad, disfruto de la tecnología, el ciclismo, los espacios de concentración, el aprendizaje autodidacta y los proyectos personales. Me gusta explorar nuevas ideas, organizar procesos, mejorar mis hábitos y mantener una mirada curiosa sobre cómo la innovación puede aplicarse en la vida diaria y en el mundo profesional.

En resumen, me considero una persona analítica, curiosa y orientada a construir. Mi objetivo es seguir desarrollándome como profesional en tecnología, aportando soluciones útiles, bien pensadas y alineadas a necesidades reales.',
    'XXXXX',
    'XXXX XXXX, XXXX',
    'https://github.com/XXXX',
    'https://www.linkedin.com/in/XXXX/'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM perfil LIMIT 1);
