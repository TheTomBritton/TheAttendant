<?php namespace ProcessWire;

/**
 * Template: home.php
 * Homepage — Filix hero banner with latest blog posts.
 * Fields: title, body, summary, featured_image
 */

// Hero section — Filix full-screen banner
$hero = "<section class='hero_warp'>";
$hero .= "<div class='container'>";
$hero .= "<div class='banner_content'>";
$hero .= "<h1 class='banner_title wow fadeInUp'>{$page->title}</h1>";
if ($page->summary) {
    $hero .= "<ul class='exp_list wow fadeInUp' data-wow-delay='0.2s'>";
    $hero .= "<li>{$page->summary}</li>";
    $hero .= "</ul>";
}
$hero .= "</div>"; // .banner_content
$hero .= "</div>"; // .container

// Social links on hero
$hero .= "<ul class='social_link'>";
$hero .= "<li><a href='#'><i class='fa fa-twitter'></i><i class='fa fa-twitter'></i></a></li>";
$hero .= "<li><a href='#'><i class='fa fa-instagram'></i><i class='fa fa-instagram'></i></a></li>";
$hero .= "<li><a href='#'><i class='fa fa-youtube-play'></i><i class='fa fa-youtube-play'></i></a></li>";
$hero .= "</ul>";

$hero .= "</section>"; // .hero_warp

// Latest blog posts
$content = '';
$latestPosts = $pages->find("template=blog-post, sort=-date, limit=6");

if ($latestPosts->count()) {
    $content .= "<section class='blog_wrap'>";
    $content .= "<div class='container'>";
    $content .= "<div class='row'>";
    $content .= "<div class='col-lg-8'>";

    foreach ($latestPosts as $post) {
        $content .= renderPostCard($post);
    }

    $content .= "</div>"; // .col-lg-8

    // Sidebar
    $content .= "<div class='col-lg-4'>";
    $content .= "<div class='blog_sidebar'>";
    $content .= renderBlogSidebar();
    $content .= "</div>";
    $content .= "</div>"; // .col-lg-4

    $content .= "</div>"; // .row
    $content .= "</div>"; // .container
    $content .= "</section>"; // .blog_wrap
}

// Body content (about teaser)
if ($page->body) {
    $content .= "<section class='blog_wrap'>";
    $content .= "<div class='container'>";
    $content .= "<div class='row justify-content-center'>";
    $content .= "<div class='col-lg-8'>";
    $content .= "<div class='blog_single_content'>";
    $content .= "<div class='blog_single_item'>{$page->body}</div>";
    $content .= "</div>";
    $content .= "</div>";
    $content .= "</div>";
    $content .= "</div>";
    $content .= "</section>";
}
