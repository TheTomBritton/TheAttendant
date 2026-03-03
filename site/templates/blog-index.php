<?php namespace ProcessWire;

/**
 * Template: blog-index.php
 * Blog listing with Filix sidebar layout.
 * Fields: title, body, summary
 */

// Inner banner
$hero = renderInnerBanner($page->title, $page->summary ?: 'News, reviews, and sound culture');

// Blog posts with optional tag filter
$selector = "template=blog-post, sort=-date, limit=12";

$tagFilter = $sanitizer->name($input->urlSegment1);
if ($tagFilter) {
    $tag = $pages->get("template=blog-tag, name={$tagFilter}");
    if ($tag->id) {
        $selector .= ", blog_tags={$tag}";
    }
}

$posts = $pages->find($selector);

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
