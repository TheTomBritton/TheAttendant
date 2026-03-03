<?php namespace ProcessWire;

/**
 * Template: _404.php
 * Custom 404 error page with Filix inner banner.
 */

$browser_title = 'Page Not Found';
$body_class = 'error-404';

// Inner banner
$hero = renderInnerBanner('404', 'Page not found');

$content = "<section class='blog_wrap'>";
$content .= "<div class='container'>";
$content .= "<div class='row justify-content-center'>";
$content .= "<div class='col-lg-8 text-center'>";

$content .= "<p>Sorry, the page you're looking for doesn't exist or has been moved.</p>";

// Search form
$content .= "<div class='contact_form' style='margin: 40px auto;'>";
$content .= "<form method='get' action='{$pages->get('/search/')->url}'>";
$content .= "<div class='form-group'>";
$content .= "<input type='search' name='q' placeholder='Search the site&hellip;' class='form-control'>";
$content .= "</div>";
$content .= "<div class='form-group'>";
$content .= "<button type='submit' class='sibmit_btn'>Search</button>";
$content .= "</div>";
$content .= "</form>";
$content .= "</div>";

// Suggested links
$content .= "<div class='suggested_links'>";
$content .= "<a href='{$home->url}'>Homepage</a>";
$content .= "<a href='{$blog_page->url}'>Blog</a>";
$content .= "<a href='{$pages->get('/search/')->url}'>Search</a>";
$content .= "</div>";

$content .= "</div>"; // .col-lg-8
$content .= "</div>"; // .row
$content .= "</div>"; // .container
$content .= "</section>"; // .blog_wrap
