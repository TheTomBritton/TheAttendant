<?php namespace ProcessWire;

/**
 * Template: basic-page.php
 * A generic content page with Filix inner banner and optional sidebar.
 * Fields: title, body, summary, featured_image, images
 */

// Inner banner
$hero = renderInnerBanner($page->title, $page->summary ?: '');

$content = '';

// Breadcrumbs
$content .= renderBreadcrumbs($page);

// Featured image
if ($page->featured_image) {
    $content .= "<div class='blog_simg_img'>";
    $content .= renderImage($page->featured_image, [600, 900, 1200], '100vw', false);
    if ($page->featured_image->description) {
        $content .= "<p class='img_caption'>{$page->featured_image->description}</p>";
    }
    $content .= "</div>";
}

// Body content
if ($page->body) {
    $content .= "<div class='blog_single_content'><div class='blog_single_item'>{$page->body}</div></div>";
}

// Image gallery
if ($page->images && $page->images->count()) {
    $content .= "<div class='gallery_wrap'>";
    $content .= "<h3 class='widget_title'>Gallery</h3>";
    $content .= "<div class='row'>";
    foreach ($page->images as $img) {
        $thumb = $img->size(400, 300);
        $content .= "<div class='col-md-4 col-6'>";
        $content .= "<div class='gallery_item'>";
        $content .= "<a href='{$img->width(1600)->url}'>";
        $content .= "<img src='{$thumb->url}' alt='{$img->description}' width='400' height='300' loading='lazy'>";
        $content .= "</a>";
        $content .= "</div>";
        $content .= "</div>";
    }
    $content .= "</div>"; // .row
    $content .= "</div>"; // .gallery_wrap
}

// Sidebar: child pages or sibling pages
$sidebarPages = $page->children->count() ? $page->children : $page->siblings("id!={$page->id}, limit=5");
if ($sidebarPages->count()) {
    $sidebar = "<div class='widget'>";
    $sidebar .= "<h3 class='widget_title'>Related Pages</h3>";
    $sidebar .= "<ul>";
    foreach ($sidebarPages as $item) {
        $sidebar .= "<li><a href='{$item->url}'>{$item->title}</a></li>";
    }
    $sidebar .= "</ul>";
    $sidebar .= "</div>";
}
