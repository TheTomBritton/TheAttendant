<?php namespace ProcessWire;

/**
 * Template: basic-page.php
 * Generic content page — Filix inner page with optional sidebar.
 * Fields: title, body, summary, featured_image, images
 */

// Banner
$subtitle = $page->summary ?: '';
$hero = renderInnerBanner($page->title, $subtitle, 'contact_banner');

// Content
$content = '';
$content .= renderBreadcrumbs($page);

// Featured image
if ($page->featured_image) {
    $content .= "<div class='blog_simg_img wow fadeInUp'>";
    $content .= renderImage($page->featured_image, [600, 900, 1200], '100vw', false);
    $content .= "</div>";
}

// Body content
if ($page->body) {
    $content .= "<div class='blog_single_item wow fadeInUp'>";
    $content .= $page->body;
    $content .= "</div>";
}

// Image gallery
if ($page->images && $page->images->count()) {
    $content .= "<div class='blog_simg_img wow fadeInUp'>";
    foreach ($page->images as $img) {
        $thumb = $img->width(800);
        $content .= "<img src='{$thumb->url}' alt='" . $sanitizer->entities($img->description) . "' class='img-fluid' style='margin-bottom: 15px;'>";
    }
    $content .= "</div>";
}

// Sidebar: child pages or sibling pages
$sidebarPages = $page->children->count() ? $page->children : $page->siblings("id!={$page->id}, limit=5");
if ($sidebarPages->count()) {
    $sidebar = "<div class='widget sidebar-widget widget_tags wow fadeInUp'>";
    $sidebar .= "<h2 class='widget_title'>Related Pages</h2>";
    $sidebar .= "<ul>";
    foreach ($sidebarPages as $item) {
        $sidebar .= "<li><a href='{$item->url}'>{$item->title}</a></li>";
    }
    $sidebar .= "</ul>";
    $sidebar .= "</div>";
}
