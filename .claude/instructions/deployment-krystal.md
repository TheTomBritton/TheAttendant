# Deploying to Krystal Shared Hosting

## Overview

Krystal is a UK-based shared hosting provider running Apache with PHP-FPM. Deployment is typically via SFTP, though SSH may be available depending on your plan.

## Pre-Deployment

Before deploying, run `/deploy-checklist` to ensure everything is ready.

### Build Production Assets
```bash
npm run build
```
This creates minified CSS (and JS if applicable) in `site/assets/dist/`.

### Update site/config.php for Production
```php
<?php namespace ProcessWire;

$config->debug = false;
$config->advanced = false;

// Database — update with Krystal credentials
$config->dbHost = 'localhost';
$config->dbName = 'your_database_name';
$config->dbUser = 'your_database_user';
$config->dbPass = 'your_database_password';
$config->dbPort = '3306';
$config->dbCharset = 'utf8mb4';
$config->dbEngine = 'InnoDB';

// URLs
$config->httpHosts = ['www.yourdomain.com', 'yourdomain.com'];
$config->https = true;

// Time zone
$config->timezone = 'Europe/London';

// Session
$config->sessionFingerprint = true;
$config->sessionCookieSecure = true;

// Admin URL (changed from default for security)
$config->urls->admin = '/your-custom-admin/';
```

## Deployment Methods

### Method 1: SFTP Upload (Most Common)

**Tools**: FileZilla, WinSCP, Cyberduck, or VS Code SFTP extension.

**Connection details** (from Krystal control panel):
- Host: your-server.krystal.hosting (check cPanel)
- Port: 22 (SFTP) or 21 (FTP — avoid if possible)
- Username: your cPanel username
- Password: your cPanel password

**Upload order:**

1. **First deployment — upload everything:**
   ```
   wire/                    ← PW core (from composer install)
   site/
   ├── templates/           ← Your template files
   ├── modules/             ← Installed modules
   ├── assets/              ← Will need write permissions
   ├── config.php           ← Production config
   ├── ready.php
   └── init.php
   index.php
   .htaccess
   ```

2. **Subsequent deployments — upload only changed files:**
   ```
   site/templates/          ← Updated template files
   site/assets/dist/        ← Rebuilt CSS/JS
   site/modules/            ← New or updated modules
   ```

**Never re-upload on update:**
- `site/assets/files/` — contains uploaded content
- `site/assets/cache/` — will regenerate
- `site/config.php` — unless config has changed

### Method 2: SSH + Git (If Available)

If your Krystal plan includes SSH access:

```bash
# SSH into server
ssh username@your-server.krystal.hosting

# Navigate to web root
cd public_html

# Clone repository (first time)
git clone https://github.com/yourusername/project-name.git .

# Install PW core
composer install --no-dev

# Set permissions
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod -R 755 site/assets/

# Pull updates (subsequent deployments)
git pull origin main
composer install --no-dev
```

### Method 3: Rsync (If SSH Available)

```bash
# From your local machine
rsync -avz --exclude='.git' \
    --exclude='node_modules' \
    --exclude='site/assets/files' \
    --exclude='site/assets/cache' \
    --exclude='site/assets/sessions' \
    --exclude='site/assets/logs' \
    --exclude='docker' \
    ./ username@server:~/public_html/
```

## Database Setup

### On Krystal (via cPanel)

1. Log into cPanel
2. Go to **MySQL Databases**
3. Create a new database
4. Create a database user with a strong password
5. Add the user to the database with **All Privileges**
6. Note the details for `site/config.php`

### Exporting from Docker (Local)

```bash
# Export from Docker
docker compose exec db mysqldump -u root -p pw_dev > database-export.sql

# Or via Adminer at http://localhost:8081
# Select database > Export > SQL
```

### Importing to Krystal

**Via cPanel phpMyAdmin:**
1. Open phpMyAdmin in cPanel
2. Select your database
3. Import tab > Choose file > Select your SQL export
4. Execute

**Via SSH (if available):**
```bash
mysql -u db_user -p db_name < database-export.sql
```

### URL Replacement

If your local dev URL differs from production, update URLs in the database:

**Via PW admin:** After importing, log into the admin. PW handles URL changes automatically based on `$config->httpHosts`.

**If manual replacement needed:**
```sql
UPDATE field_body SET data = REPLACE(data, 'http://localhost:8080', 'https://www.yourdomain.com');
```

## SSL Certificate

Krystal provides free Let's Encrypt SSL:
1. In cPanel, go to **SSL/TLS** or **Let's Encrypt**
2. Issue a certificate for your domain
3. Enable "Force HTTPS" in cPanel or via `.htaccess`

## DNS Configuration

Point your domain to Krystal's nameservers or set A/CNAME records as provided by Krystal in your welcome email or cPanel.

## Post-Deployment Checks

1. Visit the site — does it load?
2. Check the admin panel — can you log in?
3. Test all forms — do emails send?
4. Check images — are they displaying?
5. Run Lighthouse — performance score acceptable?
6. Check console for JavaScript errors
7. Test on mobile
8. Verify SSL certificate is working (green padlock)
9. Check robots.txt is accessible
10. Verify sitemap.xml generates correctly

## Environment-Specific Config

For managing different configs between dev and production, use this pattern in `site/config.php`:

```php
<?php namespace ProcessWire;

// Detect environment
$isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', 'localhost:8080', '127.0.0.1']);

if ($isLocal) {
    // Development settings
    $config->debug = true;
    $config->dbHost = 'db';
    $config->dbName = 'pw_dev';
    $config->dbUser = 'pw_user';
    $config->dbPass = 'pw_password';
    $config->httpHosts = ['localhost:8080'];
} else {
    // Production settings
    $config->debug = false;
    $config->dbHost = 'localhost';
    $config->dbName = 'your_prod_db';
    $config->dbUser = 'your_prod_user';
    $config->dbPass = 'your_prod_password';
    $config->httpHosts = ['www.yourdomain.com', 'yourdomain.com'];
    $config->https = true;
}

// Shared settings
$config->dbCharset = 'utf8mb4';
$config->dbEngine = 'InnoDB';
$config->timezone = 'Europe/London';
$config->sessionFingerprint = true;
$config->userAuthSalt = 'your-unique-64-char-salt';
```
