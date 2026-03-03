<?php namespace ProcessWire;

/**
 * ProcessWire Configuration — Sound M8
 *
 * Environment-aware: detects local Docker vs production.
 */

// ──────────────────────────────────────────────
// Environment Detection
// ──────────────────────────────────────────────
$isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', [
    'localhost',
    'localhost:8080',
    '127.0.0.1',
    '127.0.0.1:8080',
]);

// ──────────────────────────────────────────────
// Database
// ──────────────────────────────────────────────
if ($isLocal) {
    $config->dbHost = 'db';
    $config->dbName = 'sound_m8';
    $config->dbUser = 'sound_m8_user';
    $config->dbPass = 'sound_m8_dev_pass';
} else {
    // Production — update with Krystal credentials
    $config->dbHost = 'localhost';
    $config->dbName = 'CHANGE_ME';
    $config->dbUser = 'CHANGE_ME';
    $config->dbPass = 'CHANGE_ME';
}

$config->dbPort = '3306';
$config->dbCharset = 'utf8mb4';
$config->dbEngine = 'InnoDB';

// ──────────────────────────────────────────────
// Hosts & URLs
// ──────────────────────────────────────────────
if ($isLocal) {
    $config->httpHosts = ['localhost:8080', 'localhost'];
    $config->https = false;
    $config->debug = true;
} else {
    // Production — update with your domain
    $config->httpHosts = ['www.yourdomain.com', 'yourdomain.com'];
    $config->https = true;
    $config->debug = false;
}

// ──────────────────────────────────────────────
// General Settings
// ──────────────────────────────────────────────
$config->timezone = 'Europe/London';
$config->advanced = false;
$config->adminEmail = 'admin@yourdomain.com';

// ──────────────────────────────────────────────
// Installer-generated values (do not change)
// ──────────────────────────────────────────────
$config->userAuthSalt = '1e783c200b79c0e7beac7723a8aff6d92934d425';
$config->tableSalt = '1e783c200b79c0e7beac7723a8aff6d92934d425';
$config->installed = 1772491628;
$config->sessionName = 'pw751';

// ──────────────────────────────────────────────
// Session
// ──────────────────────────────────────────────
$config->sessionFingerprint = true;
if (!$isLocal) {
    $config->sessionCookieSecure = true;
}

// ──────────────────────────────────────────────
// Template Strategy: Delayed Output
// ──────────────────────────────────────────────
$config->prependTemplateFile = '_init.php';
$config->appendTemplateFile = '_main.php';

// ──────────────────────────────────────────────
// File & Image Settings
// ──────────────────────────────────────────────
$config->chmodDir = '0755';
$config->chmodFile = '0644';
$config->imageSizerOptions('webpAdd', true);

// ──────────────────────────────────────────────
// Admin
// ──────────────────────────────────────────────
$config->defaultAdminTheme = 'AdminThemeUikit';
$config->AdminThemeUikit('themeName', 'default');
