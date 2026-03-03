<?php namespace ProcessWire;

/**
 * Field, Template & Page Import Script (Standalone)
 *
 * Reference version with PW bootstrap logic.
 * For reliable use, prefer the web-accessible version: site/templates/run-import.php
 *
 * This script reads from:
 *   site/install/fields.json
 *   site/install/templates.json
 *   site/install/pages-tree.json
 */

// Bootstrap ProcessWire if not already loaded
if (!defined('PROCESSWIRE')) {
    // Inject HTTP_HOST for environment-aware config
    $_SERVER['HTTP_HOST'] = 'localhost:8080';

    $paths = [
        __DIR__ . '/../index.php',          // From scripts/
        __DIR__ . '/../../index.php',        // If nested deeper
        './index.php',                       // Current directory
    ];

    $bootstrapped = false;
    foreach ($paths as $path) {
        if (file_exists($path)) {
            include $path;
            $bootstrapped = true;
            break;
        }
    }

    if (!$bootstrapped) {
        die("Error: Could not find ProcessWire index.php. Run from the PW root directory.\n");
    }
}

// Delegate to shared import logic
require __DIR__ . '/../site/templates/run-import.php';
