# Blog Personal — Rodrigo Zurita

Blog personal académico con arquitectura de dos contenedores, diseñado como portafolio oscuro cinematográfico.

## Stack

| Capa | Tecnología |
|------|-----------|
| Servidor web | Apache 2 |
| Lenguaje | PHP 8.2 |
| Conexión a DB | PDO (php-mysql) |
| Base de datos | MariaDB 11 |
| Entorno local | Docker Compose |
| Despliegue final | Proxmox (2 contenedores LXC) |

## Arquitectura

### Local (desarrollo)

```
┌──────────────┐     ┌──────────────┐
│  app         │     │  db          │
│  Apache+PHP  │────>│  MariaDB 11  │
│  :8080 → 80  │     │  :3306       │
└──────────────┘     └──────────────┘
     │                      │
  http://               volumen
  localhost:8080        db_data
```

### Proxmox (producción)

```
┌───────────────────┐     ┌───────────────────┐
│  <DNI>A           │     │  <DNI>DB           │
│  172.16.90.157    │────>│  172.16.90.158     │
│  Apache + PHP     │     │  MariaDB           │
└───────────────────┘     └───────────────────┘
        │
  http://172.16.90.157/
```

## Levantar el proyecto localmente con Docker

El proyecto usa Docker Compose para desarrollo local. No es necesario instalar Apache, PHP ni MariaDB en tu máquina — todo corre dentro de contenedores.

### Requisitos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows/Mac) o Docker Engine + Compose (Linux)
- Git

Verificá que esté instalado:

```bash
docker --version
docker compose version
```

### Paso a paso

```bash
# 1. Clonar el repositorio (cuando esté en GitHub)
git clone <url-del-repositorio> blog-personal
cd blog-personal

# 2. Copiar el template de variables de entorno
cp .env.example .env
```

El archivo `.env` contiene las credenciales de la base de datos. Los valores por defecto funcionan para desarrollo local, pero podés cambiarlos si querés.

```bash
# 3. Construir y levantar los contenedores
docker compose up -d --build
```

Este comando hace tres cosas:
- **Construye** la imagen del servicio `app` usando el `Dockerfile` (php:8.2-apache con extensión pdo_mysql).
- **Descarga** la imagen `mariadb:11` para el servicio `db`.
- **Inicia** ambos contenedores en segundo plano (`-d`).

Podés verificar que estén corriendo:

```bash
docker compose ps
```

Deberías ver dos servicios con estado `Up`:

```
NAME                       STATUS         PORTS
blog-personal-app-1        Up             0.0.0.0:8080->80/tcp
blog-personal-db-1         Up             3306/tcp
```

> **Nota**: La primera vez que se ejecuta, MariaDB ejecuta automáticamente el archivo `db/init.sql` que crea las tablas `publicaciones` y `usuarios`, e inserta las tres publicaciones semilla.

### 4. Crear el usuario administrador

La base de datos arranca con las tablas y publicaciones semilla, pero el usuario admin hay que crearlo con un script PHP porque la contraseña debe generarse con `password_hash()`:

```bash
docker compose exec app php /var/www/html/db/init-admin.php
```

Si ves `✅ Admin user created successfully.`, ya está listo. Si ves un error de conexión, esperá unos segundos y volvé a intentarlo — MariaDB puede tardar unos segundos en estar lista.

### 5. Abrir el blog

```
http://localhost:8080
```

### 6. (Opcional) Probar que funciona

Verificá que las rutas principales respondan:

```bash
# Home público — debería responder HTTP 200
curl -s -o /dev/null -w "%{http_code}" http://localhost:8080/

# Login — debería responder HTTP 200
curl -s -o /dev/null -w "%{http_code}" http://localhost:8080/login

# Dashboard sin autenticar — debería redirigir (HTTP 301/302)
curl -s -o /dev/null -w "%{http_code}" http://localhost:8080/dashboard
```

### Detener los contenedores

```bash
# Detener sin eliminar datos
docker compose stop

# Detener y eliminar contenedores (los datos de la DB se conservan en el volumen)
docker compose down

# Detener, eliminar contenedores Y borrar la base de datos (empezar de cero)
docker compose down -v
```

### Ver logs

```bash
# Logs del servicio app (Apache/PHP)
docker compose logs app

# Logs del servicio db (MariaDB)
docker compose logs db

# Seguir logs en tiempo real
docker compose logs -f
```

## Credenciales por defecto

| Rol | Usuario | Contraseña |
|-----|---------|-----------|
| Admin | `admin` | `admin123` |

> **⚠️ IMPORTANTE**: Cambiar la contraseña antes de cualquier despliegue a producción. La contraseña se genera con `password_hash()` al ejecutar `db/init-admin.php`. Para cambiarla, editar el script o insertar un nuevo hash directamente en la base de datos.

## Administración

| Ruta | Acceso | Descripción |
|------|--------|-------------|
| `/` | Público | Home con presentación, publicaciones y link al informe PDF |
| `/login` | Público | Inicio de sesión del administrador |
| `/dashboard` | Admin | Panel de administración protegido |
| `/logout.php` | Admin | Cerrar sesión |

### Funciones del dashboard

- **Listar** publicaciones existentes
- **Crear** nuevas publicaciones (título requerido, contenido requerido, categoría opcional)
- **Editar** publicaciones existentes
- **Eliminar** publicaciones con confirmación previa
- CSRF en todas las acciones de escritura

## Publicaciones iniciales

El blog arranca con tres publicaciones semilla cargadas desde `db/init.sql`:

1. **Presentación del Blog Personal** — Académico
2. **Tecnologías utilizadas** — Tecnología
3. **Implementación en Proxmox** — Infraestructura

Estas publicaciones son editables desde el dashboard.

## Seguridad

- Contraseña hasheada con `password_hash()` (bcrypt)
- Verificación con `password_verify()`
- Consultas preparadas con PDO (sin inyección SQL)
- CSRF token en formularios de crear, editar y eliminar
- Escape de salida con `htmlspecialchars()` (XSS)
- Sesión PHP con `session_regenerate_id()` post-login
- Redirección de dashboard no autenticado a `/login`

## Diseño

- **North Star**: "The Night Desk Portfolio"
- Fondo oscuro cinematográfico (`#121820`)
- Acento cyan restringido (`#0F8FBD`)
- Tipografía sans-serif fuerte (Montserrat)
- Hero con imagen personal estructural
- Grid visual tipo portfolio para publicaciones
- Estados: hover, focus, active, empty, error, success
- Responsive: mobile (< 768px), tablet (768–1023px), desktop (≥ 1024px)
- Sin sombras pesadas, sin gradientes AI-purple, sin fondos blancos genéricos

## Despliegue en Proxmox

```bash
# 1. Preparar el contenedor APP (<DNI>A, 172.16.90.157)
ssh root@172.16.90.157
apt update
apt install apache2 php libapache2-mod-php php-mysql git -y

# 2. Clonar el repositorio
rm -rf /var/www/html/*
cd /var/www/html
git clone <url-del-repositorio> .
chown -R www-data:www-data /var/www/html

# 3. Configurar conexión a la base de datos
# Editar /var/www/html/.env
# DB_HOST=172.16.90.158

# 4. Preparar el contenedor DB (<DNI>DB, 172.16.90.158)
ssh root@172.16.90.158
apt update
apt install mariadb-server mariadb-client -y

# 5. Configurar MariaDB (ver instalar_mariadb_ubuntu20_128mb_proxmox.md)
# Crear base de datos blogdb, usuario bloguser, importar db/init.sql

# 6. Seedear admin
docker compose exec app php /var/www/html/db/init-admin.php
# O en Proxmox sin Docker:
php /var/www/html/db/init-admin.php
```

## Estructura del proyecto

```
blog_personal/
├── .env                    # Variables de entorno (no versionar)
├── .env.example            # Template de variables de entorno
├── .gitignore
├── docker-compose.yml      # Servicios app + db
├── Dockerfile              # php:8.2-apache + pdo_mysql
├── db/
│   ├── init.sql            # Schema + datos semilla
│   └── init-admin.php      # Creación del usuario admin
├── index.php               # Home público
├── db.php                  # Conexión PDO
├── auth.php                # Guard de autenticación
├── logout.php              # Cierre de sesión
├── guardar_publicacion.php # Handler de creación
├── login/
│   └── index.php           # Formulario de login
├── dashboard/
│   ├── index.php           # Panel + lista + crear
│   ├── editar.php          # Editar publicación
│   └── eliminar.php        # Eliminar con confirmación
└── assets/
    ├── css/
    │   └── style.css       # Sistema de diseño
    ├── img/
    │   └── foto-personal.jpg  # Placeholder
    └── docs/
        └── informe-tpf.pdf    # Placeholder
```

## Licencia

Trabajo Práctico Final — Arquitectura y Virtualización.
