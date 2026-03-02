<?php namespace ProcessWire;

/**
 * ProcessWire Configuration
 *
 * This file is environment-aware: it detects local vs production
 * and applies appropriate settings automatically.
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
    $config->dbHost = 'db';           // Docker service name
    $config->dbName = 'pw_dev';
    $config->dbUser = 'pw_user';
    $config->dbPass = 'pw_password';
} else {
    // Production — update these with Krystal credentials
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

// Authentication salt — CHANGE THIS to a unique random string
$config->userAuthSalt = 'CHANGE-THIS-TO-A-UNIQUE-64-CHARACTER-RANDOM-STRING';

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
$config->imageSizerOptions('webpAdd', true);  // Auto-generate WebP variants

// ──────────────────────────────────────────────
// Admin
// ──────────────────────────────────────────────
// Uncomment and change for production (security hardening)
// $config->urls->admin = '/your-custom-admin/';
