<?php namespace ProcessWire;

/**
 * Template: _404.php
 * Custom 404 error page — Filix inner banner style.
 */

$browser_title = 'Page Not Found';
$body_class = 'error-404';

// Banner
$hero = renderInnerBanner('Page Not Found', 'Sorry, the page you\'re looking for doesn\'t exist.', 'contact_banner');

// Content
$content = "<section class='blog_wrap pd_120'>";
$content .= "<div class='container'>";
$content .= "<div class='row justify-content-center'>";
$content .= "<div class='col-lg-8 col-md-12'>";

$content .= "<div class='error-page wow fadeInUp'>";
$content .= "<h1>404</h1>";
$content .= "<p>The page you're looking for doesn't exist or has been moved.</p>";
$content .= "<p><a href='{$home->url}' class='read_more'>Return to the homepage</a></p>";

$content .= "<div class='suggestions'>";
$content .= "<h2>You might be looking for:</h2>";
$content .= "<ul>";
foreach ($home->children('limit=6') as $item) {
    $content .= "<li><a href='{$item->url}'>{$item->title}</a></li>";
}
$content .= "</ul>";
$content .= "</div>";

$content .= "</div>";
$content .= "</div>";
$content .= "</div>";
$content .= "</div>";
$content .= "</section>";

// No sidebar
$sidebar = '';
