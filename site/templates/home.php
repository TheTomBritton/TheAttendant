<?php namespace ProcessWire;

/**
 * Template: home.php
 * The homepage — hero section + latest blog posts.
 * Fields: title, body, summary, featured_image
 */

// Hero section
$hero = "<section class='hero'>";
$hero .= "<div class='container'>";
$hero .= "<h1>{$page->title}</h1>";
if ($page->summary) {
    $hero .= "<p class='lead'>{$page->summary}</p>";
}
$hero .= "</div>";
$hero .= "</section>";

// Main content
$content = '';

if ($page->body) {
    $content .= "<div class='intro'>{$page->body}</div>";
}

// Latest blog posts
$latestPosts = $pages->find("template=blog-post, sort=-date, limit=6");
if ($latestPosts->count()) {
    $content .= "<section class='latest-posts'>";
    $content .= "<h2>Latest Posts</h2>";
    $content .= "<div class='posts-grid'>";
    foreach ($latestPosts as $post) {
        $content .= renderPostCard($post);
    }
    $content .= "</div>";

    $blogIndex = $pages->get('template=blog-index');
    if ($blogIndex->id) {
        $content .= "<p class='view-all'><a href='{$blogIndex->url}' class='btn'>View all posts</a></p>";
    }
    $content .= "</section>";
}

// Feature child pages as cards (About, etc.)
$children = $page->children('limit=6, template!=blog-index');
if ($children->count()) {
    $content .= "<section class='featured-pages'>";
    $content .= "<div class='card-grid'>";
    foreach ($children as $child) {
        $content .= renderPageCard($child);
    }
    $content .= "</div>";
    $content .= "</section>";
}
