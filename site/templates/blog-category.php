<?php namespace ProcessWire;

/**
 * Template: blog-category.php
 * Displays posts filtered by this category.
 * Fields: title, body, featured_image, seo_title, seo_description
 */

$browser_title = $page->get('seo_title|title') . ' — Blog';
$meta_description = $page->get('seo_description|body|');

$content = renderBreadcrumbs($page);

$content .= "<h1>{$page->title}</h1>";
if ($page->body) {
    $content .= "<div class='category-description'>{$page->body}</div>";
}

// Posts in this category
$posts = $pages->find("template=blog-post, blog_categories=$page, sort=-date, limit=12");

if ($posts->count()) {
    $content .= "<div class='posts-grid'>";
    foreach ($posts as $post) {
        $content .= renderPostCard($post);
    }
    $content .= "</div>";
    $content .= renderPagination($posts);
} else {
    $content .= "<p>No posts in this category yet.</p>";
}

// Link back to all posts
$blogIndex = $pages->get('template=blog-index');
if ($blogIndex->id) {
    $content .= "<p><a href='{$blogIndex->url}'>&larr; View all posts</a></p>";
}
