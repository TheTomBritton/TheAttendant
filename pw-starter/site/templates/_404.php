<?php namespace ProcessWire;

/**
 * Template: _404.php
 * Custom 404 error page.
 */

$browser_title = 'Page Not Found';
$body_class = 'error-404';

$content = "<div class='error-page'>";
$content .= "<h1>Page not found</h1>";
$content .= "<p>Sorry, the page you're looking for doesn't exist or has been moved.</p>";
$content .= "<p><a href='{$home->url}'>Return to the homepage</a></p>";

// Suggest some pages
$content .= "<div class='suggestions'>";
$content .= "<h2>You might be looking for:</h2>";
$content .= "<ul>";
foreach ($home->children('limit=6') as $item) {
    $content .= "<li><a href='{$item->url}'>{$item->title}</a></li>";
}
$content .= "</ul>";
$content .= "</div>";

$content .= "</div>";
