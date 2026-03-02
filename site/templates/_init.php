<?php namespace ProcessWire;

/**
 * _init.php — Auto-prepended before every template
 *
 * Sets up default region variables that templates can override.
 * Include shared helper functions here.
 */

// Include helper functions
include_once('./_func.php');

// ──────────────────────────────────────────────
// Default region variables
// Templates override these as needed
// ──────────────────────────────────────────────

// Browser title — falls back through SEO title, then page title
$browser_title = $page->get('seo_title|title');

// Meta description — falls back through SEO description, summary, then empty
$meta_description = $page->get('seo_description|summary|');

// Body class — template name by default
$body_class = $page->template->name;

// Content regions — templates populate these
$content = '';
$sidebar = '';
$hero = '';

// Extra head/foot — for template-specific CSS/JS or structured data
$extra_head = '';
$extra_foot = '';

// Site-wide variables
$site_name = $pages->get('/')->title;
$home = $pages->get('/');
