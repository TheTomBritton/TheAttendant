# /docker-up — Start Local Docker Development Environment

## Purpose
Start (or restart) the local Docker development environment for ProcessWire.

## Workflow

### Step 1: Check Prerequisites
Verify the following exist:
- `docker/docker-compose.yml`
- `docker/Dockerfile`
- `docker/.env` (if missing, copy from `docker/.env.example` and prompt for values)

### Step 2: Check Docker Status
```bash
docker --version
docker compose version
```
If Docker isn't running, instruct the operator to start Docker Desktop.

### Step 3: Start Containers
```bash
cd docker
docker compose up -d --build
```

### Step 4: Verify Services
Check that all services are running:
- **PHP/Apache**: http://localhost:8080
- **MariaDB**: port 3306
- **Adminer** (DB admin): http://localhost:8081

### Step 5: First Run Setup
If this is the first run (no `wire/` symlink exists in the project root):

1. Run `composer install` inside the container:
   ```bash
   docker exec <project>-web composer install --no-interaction
   ```

2. Bootstrap PW files in the web root (run inside container):
   ```bash
   docker exec <project>-web bash -c '
   cd /var/www/html
   ln -sf vendor/processwire/processwire/wire wire
   cp vendor/processwire/processwire/index.php index.php
   cp vendor/processwire/processwire/install.php install.php
   cp vendor/processwire/processwire/htaccess.txt .htaccess
   cp vendor/processwire/processwire/site-blank/install/install.sql site/install/install.sql
   cp vendor/processwire/processwire/site-blank/install/info.php site/install/info.php
   cp -r vendor/processwire/processwire/site-blank/install/files site/install/files 2>/dev/null
   cp vendor/processwire/processwire/site-blank/templates/admin.php site/templates/admin.php
   mkdir -p site/modules site/assets/files site/assets/cache site/assets/logs site/assets/sessions
   chmod -R 777 site/assets/ site/install/ site/modules/
   chown www-data:www-data index.php install.php
   '
   ```

3. Confirm PW installer is accessible at http://localhost:8080

4. Guide through the web installer:
   - Database host: `db` (**not** localhost — that triggers Unix socket errors)
   - Database name/user/pass: values from `docker/.env`
   - Time zone: Europe/London
   - Select "Blank" site profile

5. After installation, clean up `site/config.php` — the PW installer appends duplicate config blocks that conflict with the environment-aware config. Remove duplicates, keep installer-generated values (salts, session name, installed timestamp).

6. Run the field/template import script using CLI with HTTP_HOST set:
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
   Or place the import script in `site/templates/` and access via browser as superuser, then delete it.

### Step 6: Output Status
Display:
- Container status (running/stopped)
- Access URLs
- Database credentials (from .env)
- Log tail command: `docker compose logs -f`

## Common Issues

Consult `.claude/instructions/docker-setup.md` for full troubleshooting, including:
- Port conflicts
- "No installation profile" error
- "Missing template file: admin.php"
- "Directory /site/modules/ does not exist"
- 404 after PW installation
- Git clone failing inside Docker
- Database socket connection errors
- File permission issues on macOS

## Stopping the Environment
```bash
cd docker
docker compose down        # Stop containers
docker compose down -v     # Stop and remove database volume (fresh start)
```
