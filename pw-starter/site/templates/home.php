<?php namespace ProcessWire;

/**
 * Template: home.php
 * The homepage template.
 * Fields: title, body, summary, featured_image
 */

// Hero section
$hero = "<section class='hero'>";
$hero .= "<div class='container'>";
$hero .= "<h1>{$page->title}</h1>";
if ($page->summary) {
    $hero .= "<p class='lead'>{$page->summary}</p>";
}
$hero .= "</div>";
$hero .= "</section>";

// Main content
$content = '';

if ($page->body) {
    $content .= "<div class='intro'>{$page->body}</div>";
}

// Feature child pages as cards
$children = $page->children('limit=6');
if ($children->count()) {
    $content .= "<section class='featured-pages'>";
    $content .= "<div class='card-grid'>";
    foreach ($children as $child) {
        $content .= renderPageCard($child);
    }
    $content .= "</div>";
    $content .= "</section>";
}
