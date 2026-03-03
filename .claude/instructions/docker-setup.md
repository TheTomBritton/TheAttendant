# Docker Local Development Setup

## Prerequisites

### Install Docker Desktop
- **Windows**: Download from https://www.docker.com/products/docker-desktop/ — requires WSL2 enabled
- **macOS**: Download from the same URL — works on both Intel and Apple Silicon

After installation, verify:
```bash
docker --version
docker compose version
```

## Architecture

The Docker setup provides three services:

| Service | Purpose | Local URL |
|---|---|---|
| **web** | Apache + PHP 8.2 (ProcessWire) | http://localhost:8080 |
| **db** | MariaDB 10.6 (database) | localhost:3306 |
| **adminer** | Database management UI | http://localhost:8081 |

## Configuration

### docker/.env
Copy `docker/.env.example` to `docker/.env` and adjust:

```env
# Project
PROJECT_NAME=my-client-site
COMPOSE_PROJECT_NAME=my-client-site

# Web server
WEB_PORT=8080
ADMINER_PORT=8081

# Database
DB_NAME=pw_dev
DB_USER=pw_user
DB_PASS=pw_password
DB_ROOT_PASS=root_password
DB_PORT=3306

# ProcessWire admin (for initial setup reference)
PW_ADMIN_USER=admin
PW_ADMIN_PASS=change-this-password
```

## Getting Started

### First Time Setup

```bash
# 1. Clone the repo and create a project branch
git clone https://github.com/yourusername/project-name.git my-client-site
cd my-client-site
git checkout -b my-project-name

# 2. Configure Docker environment
cp docker/.env.example docker/.env
# Edit docker/.env with your project name and credentials

# 3. Install frontend dependencies (host machine)
npm install

# 4. Start Docker
cd docker
docker compose up -d --build
cd ..

# 5. Install PW core via Composer INSIDE the container
docker exec <project>-web composer install --no-interaction

# 6. Bootstrap PW files in the web root (see below)

# 7. Visit http://localhost:8080 to run the PW installer
```

### Bootstrapping ProcessWire (Critical First-Run Steps)

Composer installs ProcessWire into `vendor/processwire/processwire/`. The web root needs several files that PW expects at the top level. **Run these inside the container after `composer install`:**

```bash
docker exec <project>-web bash -c '
cd /var/www/html

# Symlink the wire directory (PW core)
ln -sf vendor/processwire/processwire/wire wire

# Copy (not symlink) index.php — symlinks cause path detection issues
cp vendor/processwire/processwire/index.php index.php
cp vendor/processwire/processwire/install.php install.php
cp vendor/processwire/processwire/htaccess.txt .htaccess

# The PW installer needs the blank profile install files
cp vendor/processwire/processwire/site-blank/install/install.sql site/install/install.sql
cp vendor/processwire/processwire/site-blank/install/info.php site/install/info.php
cp -r vendor/processwire/processwire/site-blank/install/files site/install/files

# Copy admin.php template (required for PW admin to work)
cp vendor/processwire/processwire/site-blank/templates/admin.php site/templates/admin.php

# Ensure required directories exist with write permissions
mkdir -p site/modules site/assets/files site/assets/cache site/assets/logs site/assets/sessions
chmod -R 777 site/assets/ site/install/ site/modules/
chown www-data:www-data index.php install.php
'
```

**Why these steps are needed:**
- `wire/` symlink: PW core lives in `vendor/` but PW expects it at the root
- `index.php` must be a **copy**, not a symlink — symlinks confuse PW's path detection and cause 404s after installation
- `install.sql` + `info.php`: PW installer won't detect a profile without these
- `admin.php`: Missing this causes "Missing or non-readable template file: admin.php" errors
- `site/modules/`: PW installer fails if this directory doesn't exist

### ProcessWire Installation (Web Installer)

When you visit http://localhost:8080, the PW installer will run:

1. **Database settings:**
   - Host: `db` (the Docker service name — **not** `localhost`, which triggers Unix socket connection)
   - Name: value from `DB_NAME` in `.env`
   - User: value from `DB_USER` in `.env`
   - Password: value from `DB_PASS` in `.env`
   - Port: `3306`

2. **Admin account:**
   - Choose a username and password
   - Set admin email

3. **Time zone:** Europe/London

4. **Profile:** Blank (we use our own templates)

5. **After installation — clean up config.php:**
   The PW installer **appends** its own config block to `site/config.php`, creating duplicate/conflicting values with the environment-aware config already in the file. After installation, edit `site/config.php` to remove the duplicates and keep only the clean environment-aware version with the installer-generated values (`userAuthSalt`, `tableSalt`, `installed`, `sessionName`) merged in.

6. **Import fields and templates** using the import runner script (see below).

### Running Import Scripts

The PW CLI bootstrap does **not** set `$_SERVER['HTTP_HOST']`, which breaks environment-aware config files (the `$isLocal` detection falls through to production `CHANGE_ME` values). Two workarounds:

**Option A: Set HTTP_HOST in CLI (recommended):**
```bash
docker exec <project>-web bash -c '
export HTTP_HOST=localhost:8080
php -d variables_order=EGPCS -r "
\$_SERVER[\"HTTP_HOST\"] = \"localhost:8080\";
include \"/var/www/html/index.php\";
include \"/var/www/html/site/templates/run-import.php\";
"
'
```

**Option B: Temporary web-accessible script:**
Place the import script in `site/templates/`, access it via browser as a logged-in superuser, then delete it immediately.

**Never** run `php scripts/install-fields.php` directly from CLI without setting HTTP_HOST — it will fail with database connection errors.

### Daily Development

```bash
# Start environment
cd docker && docker compose up -d

# Watch frontend changes (in project root, separate terminal)
npm run dev

# View logs
cd docker && docker compose logs -f

# Stop environment
cd docker && docker compose down
```

## File Watching

The Docker setup mounts the project directory into the container, so file changes are reflected immediately — no restart needed for PHP changes.

For Tailwind CSS, run the watcher in a separate terminal:
```bash
npm run dev
# This runs: tailwindcss -i ./site/assets/src/app.css -o ./site/assets/dist/app.css --watch
```

## Database Management

### Adminer (Web UI)
Visit http://localhost:8081
- System: MySQL
- Server: `db`
- Username: value from `DB_USER`
- Password: value from `DB_PASS`
- Database: value from `DB_NAME`

### Command Line
```bash
# Connect to MySQL CLI
docker compose exec db mysql -u pw_user -p pw_dev

# Export database
docker compose exec db mysqldump -u pw_user -p pw_dev > ../backup.sql

# Import database
docker compose exec -T db mysql -u pw_user -p pw_dev < ../backup.sql
```

## Troubleshooting

### Port already in use
Change `WEB_PORT` or `DB_PORT` in `docker/.env`, then restart:
```bash
docker compose down && docker compose up -d
```

### Permission issues
```bash
# Fix file ownership (run from project root)
docker compose exec web chown -R www-data:www-data /var/www/html/site/assets/
```

### Container won't start
```bash
# Check logs
docker compose logs web
docker compose logs db

# Rebuild from scratch
docker compose down -v  # Removes volumes (database data!)
docker compose up -d --build
```

### Composer/PHP issues inside container
```bash
# Run commands inside the PHP container
docker compose exec web bash
# Now you're inside the container
composer install
php -v
```

### "No installation profile" error
The PW installer can't find `install.sql` and `info.php` in `site/install/`. Copy them from the blank profile:
```bash
docker exec <project>-web bash -c '
cp vendor/processwire/processwire/site-blank/install/install.sql site/install/install.sql
cp vendor/processwire/processwire/site-blank/install/info.php site/install/info.php
'
```

### "Missing template file: admin.php" error
Copy `admin.php` from the blank profile:
```bash
docker exec <project>-web cp vendor/processwire/processwire/site-blank/templates/admin.php site/templates/admin.php
```

### "Directory /site/modules/ does not exist" error
```bash
mkdir -p site/modules && chmod 777 site/modules
```

### 404 after PW installation completes
Usually caused by `index.php` being a symlink. Replace with an actual copy:
```bash
docker exec <project>-web bash -c 'rm index.php && cp vendor/processwire/processwire/index.php index.php'
```

### Git clone fails inside Docker container
Docker containers often can't resolve GitHub DNS. **Always clone modules from the host machine** — the `site/modules/` directory is volume-mounted and immediately visible inside the container.

### Database "SQLSTATE[HY000] [2002] No such file or directory"
The DB host must be `db` (the Docker service name), not `localhost`. Using `localhost` causes PHP to attempt a Unix socket connection which doesn't exist in the container.

### Slow file system on macOS
Docker on macOS can be slow with mounted volumes. If performance is poor:
1. Open Docker Desktop > Settings > Resources > File Sharing
2. Ensure your project directory is in the list
3. Consider using Docker's `cached` mount option (already configured in docker-compose.yml)

### Windows-specific: line endings
Ensure Git is configured to use LF line endings:
```bash
git config core.autocrlf input
```

## Resetting Everything

```bash
# Nuclear option — removes all containers, volumes, and images
cd docker
docker compose down -v --rmi all

# Then rebuild
docker compose up -d --build
```
