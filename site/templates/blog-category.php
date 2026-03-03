<?php namespace ProcessWire;

/**
 * Template: blog-category.php
 * Category-filtered blog listing with Filix sidebar layout.
 * Fields: title, body, summary
 */

// Inner banner
$hero = renderInnerBanner($page->title, $page->summary ?: '');

// Posts in this category
$posts = $pages->find("template=blog-post, blog_categories={$page}, sort=-date, limit=12");

$content = '';

// Breadcrumbs
$content .= renderBreadcrumbs($page);

if ($page->body) {
    $content .= "<div class='blog_single_content'><div class='blog_single_item'>{$page->body}</div></div>";
}

foreach ($posts as $post) {
    $content .= renderPostCard($post);
}

// Pagination
$content .= renderPagination($posts);

// Sidebar
$sidebar = renderBlogSidebar();
