<?php namespace ProcessWire;

/**
 * Template: home.php
 * Homepage — Filix hero banner + latest blog posts.
 * Fields: title, body, summary, featured_image
 */

// Hero banner
$hero = "<section class='hero_warp'>";
$hero .= "<div class='container'>";
$hero .= "<div class='row d-flex align-items-center'>";
$hero .= "<div class='col-md-12 col-12'>";
$hero .= "<div class='banner_content'>";
$hero .= "<h1 class='banner_title'>{$page->title}</h1>";
if ($page->summary) {
    $hero .= "<p class='banner_para wow fadeInUp'>{$page->summary}</p>";
}
$hero .= "</div>";
$hero .= "</div>";
$hero .= "</div>";
$hero .= "</div>";
$hero .= "</section>";

// Main content — blog listing style
$content = "<section class='blog_wrap pd_120'>";
$content .= "<div class='container'>";

if ($page->body) {
    $content .= "<div class='row'><div class='col-12'>";
    $content .= "<div class='wow fadeInUp'>{$page->body}</div>";
    $content .= "</div></div>";
}

// Latest blog posts
$latestPosts = $pages->find("template=blog-post, sort=-date, limit=6");
if ($latestPosts->count()) {
    $content .= "<div class='row justify-content-center'>";
    $content .= "<div class='col-lg-8 col-md-12 col-sm-12 col-xs-12'>";
    $content .= "<div class='blog_content'>";
    foreach ($latestPosts as $post) {
        $content .= renderPostCard($post);
    }
    $content .= "</div>";

    $blogIndex = $pages->get('template=blog-index');
    if ($blogIndex->id) {
        $content .= "<div class='text-center wow fadeInUp' style='margin-top: 40px;'>";
        $content .= "<a href='{$blogIndex->url}' class='read_more'>View all posts</a>";
        $content .= "</div>";
    }
    $content .= "</div>"; // .col-lg-8

    // Sidebar with recent posts + categories
    $content .= "<div class='col-lg-4 col-md-12 col-sm-12 col-xs-12'>";
    $content .= "<div class='blog_sidebar'>";
    $content .= renderBlogSidebar($page);
    $content .= "</div>";
    $content .= "</div>";

    $content .= "</div>"; // .row
}

$content .= "</div>"; // .container
$content .= "</section>";

// Override sidebar to empty — home handles its own layout
$sidebar = '';
