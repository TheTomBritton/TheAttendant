<?php namespace ProcessWire;

/**
 * Template: blog-index.php
 * Paginated blog listing with sidebar. Filix blog-sidebar layout.
 * Fields: title, body, summary, seo_title, seo_description
 *
 * URL segments enabled:
 *   /blog/tag/tag-name/ — filter by tag
 */

// Check for tag filtering via URL segment
$filterTag = '';
$filterLabel = '';
if ($input->urlSegment1 === 'tag' && $input->urlSegment2) {
    $filterTag = $sanitizer->selectorValue($input->urlSegment2);
    $filterLabel = $sanitizer->entities($input->urlSegment2);
}

// Banner
if ($filterTag) {
    $hero = renderInnerBanner("Posts tagged: {$filterLabel}", '');
} else {
    $subtitle = $page->summary ?: '';
    $hero = renderInnerBanner($page->title, $subtitle, 'contact_banner');
}

// Build the query
$selector = "template=blog-post, sort=-date, limit=12";
if ($filterTag) {
    $selector .= ", blog_tags.name=$filterTag";
}

$posts = $pages->find($selector);

// Content — blog posts listing
$content = '';
$content .= renderBreadcrumbs($page);

if ($filterTag) {
    $content .= "<p class='wow fadeInUp'><a href='{$page->url}'>&larr; View all posts</a></p>";
}

if (!$filterTag && $page->body) {
    $content .= "<div class='wow fadeInUp'>{$page->body}</div>";
}

if ($posts->count()) {
    foreach ($posts as $post) {
        $content .= renderPostCard($post);
    }
    $content .= renderPagination($posts);
} else {
    $content .= "<p class='wow fadeInUp'>No posts found.</p>";
}

// Sidebar
$sidebar = renderBlogSidebar($page);
