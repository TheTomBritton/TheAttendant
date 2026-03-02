<?php namespace ProcessWire;

/**
 * Template: blog-index.php
 * Paginated blog listing with tag filtering via URL segments.
 * Fields: title, body, summary, seo_title, seo_description
 *
 * URL segments enabled:
 *   /blog/tag/tag-name/ — filter by tag
 */

$content = renderBreadcrumbs($page);

// Check for tag filtering via URL segment
$filterTag = '';
$filterLabel = '';
if ($input->urlSegment1 === 'tag' && $input->urlSegment2) {
    $filterTag = $sanitizer->selectorValue($input->urlSegment2);
    $filterLabel = $sanitizer->entities($input->urlSegment2);
}

// Build the query
$selector = "template=blog-post, sort=-date, limit=12";
if ($filterTag) {
    $selector .= ", blog_tags.name=$filterTag";
    $content .= "<h1>Posts tagged: {$filterLabel}</h1>";
    $content .= "<p><a href='{$page->url}'>&larr; View all posts</a></p>";
} else {
    $content .= "<h1>{$page->title}</h1>";
    if ($page->body) {
        $content .= "<div class='blog-intro'>{$page->body}</div>";
    }
}

$posts = $pages->find($selector);

if ($posts->count()) {
    // HTMX target for dynamic loading
    $content .= "<div class='posts-grid' id='posts-grid'>";
    foreach ($posts as $post) {
        $content .= renderPostCard($post);
    }
    $content .= "</div>";

    // Pagination
    $content .= renderPagination($posts);
} else {
    $content .= "<p>No posts found.</p>";
}

// Sidebar: categories and popular tags
$sidebar = '';

// Categories
$categories = $pages->find("template=blog-category, sort=title");
if ($categories->count()) {
    $sidebar .= "<div class='sidebar-section'>";
    $sidebar .= "<h3>Categories</h3>";
    $sidebar .= "<ul>";
    foreach ($categories as $cat) {
        $postCount = $pages->count("template=blog-post, blog_categories=$cat");
        $sidebar .= "<li><a href='{$cat->url}'>{$cat->title}</a> <span>({$postCount})</span></li>";
    }
    $sidebar .= "</ul>";
    $sidebar .= "</div>";
}

// Tags
$tags = $pages->find("template=blog-tag, sort=title");
if ($tags->count()) {
    $sidebar .= "<div class='sidebar-section'>";
    $sidebar .= "<h3>Tags</h3>";
    $sidebar .= "<div class='tag-cloud'>";
    foreach ($tags as $tag) {
        $sidebar .= "<a href='{$page->url}tag/{$tag->name}/' class='tag'>{$tag->title}</a> ";
    }
    $sidebar .= "</div>";
    $sidebar .= "</div>";
}
