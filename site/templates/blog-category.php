<?php namespace ProcessWire;

/**
 * Template: blog-category.php
 * Posts filtered by category — Filix blog-sidebar layout.
 * Fields: title, body, featured_image, seo_title, seo_description
 */

$browser_title = $page->get('seo_title|title') . ' — Blog';
$meta_description = $page->get('seo_description|body|');

// Banner
$hero = renderInnerBanner($page->title, '', 'contact_banner');

// Content — post listing for this category
$content = '';
$content .= renderBreadcrumbs($page);

if ($page->body) {
    $content .= "<div class='wow fadeInUp'>{$page->body}</div>";
}

$posts = $pages->find("template=blog-post, blog_categories=$page, sort=-date, limit=12");

if ($posts->count()) {
    foreach ($posts as $post) {
        $content .= renderPostCard($post);
    }
    $content .= renderPagination($posts);
} else {
    $content .= "<p class='wow fadeInUp'>No posts in this category yet.</p>";
}

$blogIndex = $pages->get('template=blog-index');
if ($blogIndex->id) {
    $content .= "<p class='wow fadeInUp'><a href='{$blogIndex->url}'>&larr; View all posts</a></p>";
}

// Sidebar
$sidebar = renderBlogSidebar($page);
