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
If this is the first run (no `wire/` directory exists):

1. Confirm `composer install` has been run
2. Check that the PW installer is accessible at http://localhost:8080
3. Guide through the web installer:
   - Database: use values from `docker/.env`
   - Admin user: use values from `docker/.env`
   - Time zone: Europe/London
   - Select "Blank" site profile
4. After installation, remind to run the field/template import script

### Step 6: Output Status
Display:
- Container status (running/stopped)
- Access URLs
- Database credentials (from .env)
- Log tail command: `docker compose logs -f`

## Common Issues

### Port conflicts
If port 8080 or 3306 is in use, update `docker/.env` with alternative ports and restart.

### File permission issues (macOS)
ProcessWire needs write access to `site/assets/`. The Docker config handles this, but if issues occur:
```bash
chmod -R 777 site/assets/
```

### Database connection refused
Wait 10–15 seconds after `docker compose up` for MariaDB to initialise. Check logs:
```bash
docker compose logs db
```

## Stopping the Environment
```bash
cd docker
docker compose down        # Stop containers
docker compose down -v     # Stop and remove database volume (fresh start)
```
