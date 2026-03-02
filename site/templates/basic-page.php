<?php namespace ProcessWire;

/**
 * Template: basic-page.php
 * A generic content page with optional featured image and sidebar.
 * Fields: title, body, summary, featured_image, images
 */

// Breadcrumbs
$content = renderBreadcrumbs($page);

// Page heading
$content .= "<h1>{$page->title}</h1>";

// Featured image (above the fold — no lazy loading)
if ($page->featured_image) {
    $content .= "<figure class='featured-image'>";
    $content .= renderImage($page->featured_image, [600, 900, 1200], '100vw', false);
    if ($page->featured_image->description) {
        $content .= "<figcaption>{$page->featured_image->description}</figcaption>";
    }
    $content .= "</figure>";
}

// Body content
if ($page->body) {
    $content .= "<div class='body-content'>{$page->body}</div>";
}

// Image gallery
if ($page->images && $page->images->count()) {
    $content .= "<section class='gallery'>";
    $content .= "<h2>Gallery</h2>";
    $content .= "<div class='gallery-grid'>";
    foreach ($page->images as $img) {
        $thumb = $img->size(400, 300);
        $content .= "<figure>";
        $content .= "<a href='{$img->width(1600)->url}'>";
        $content .= "<img src='{$thumb->url}' alt='{$img->description}' width='400' height='300' loading='lazy'>";
        $content .= "</a>";
        if ($img->description) {
            $content .= "<figcaption>{$img->description}</figcaption>";
        }
        $content .= "</figure>";
    }
    $content .= "</div>";
    $content .= "</section>";
}

// Sidebar: child pages or sibling pages
$sidebarPages = $page->children->count() ? $page->children : $page->siblings("id!={$page->id}, limit=5");
if ($sidebarPages->count()) {
    $sidebar = "<h3>Related Pages</h3>";
    $sidebar .= "<ul>";
    foreach ($sidebarPages as $item) {
        $sidebar .= "<li><a href='{$item->url}'>{$item->title}</a></li>";
    }
    $sidebar .= "</ul>";
}
