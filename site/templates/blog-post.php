<?php namespace ProcessWire;

/**
 * Template: blog-post.php
 * Single blog article with Filix blog-single layout.
 * Fields: title, body, summary, featured_image, date, blog_categories, blog_tags, blog_authors
 */

// Blog single banner with post meta
$hero = "<section class='hero_warp inner_banner blog_single_banner'>";
$hero .= "<div class='container'>";
$hero .= "<div class='banner_content'>";
$hero .= "<h1 class='banner_title'>{$page->title}</h1>";

// Post meta in banner
$hero .= "<ul class='post_info'>";

$date = date('j F Y', $page->getUnformatted('date'));
$hero .= "<li><span class='post_time'>{$date}</span></li>";

if ($page->blog_categories && $page->blog_categories->count()) {
    foreach ($page->blog_categories as $cat) {
        $hero .= "<li><a href='{$cat->url}'>{$cat->title}</a></li>";
    }
}

if ($page->blog_authors && $page->blog_authors->count()) {
    $author = $page->blog_authors->first();
    $hero .= "<li><span class='author'>{$author->title}</span></li>";
}

$hero .= "</ul>";
$hero .= "</div>"; // .banner_content
$hero .= "</div>"; // .container
$hero .= "</section>";

// Article content
$content = '';

$content .= "<section class='blog_wrap blog_single_wrap'>";
$content .= "<div class='container'>";
$content .= "<div class='row'>";
$content .= "<div class='col-lg-8'>";
$content .= "<div class='blog_single_content'>";

$content .= "<div class='blog_single_item'>";

// Featured image
if ($page->featured_image) {
    $content .= "<div class='blog_simg_img'>";
    $content .= renderImage($page->featured_image, [600, 900, 1200], '100vw', false);
    if ($page->featured_image->description) {
        $content .= "<p class='img_caption'>{$page->featured_image->description}</p>";
    }
    $content .= "</div>";
}

// Body content
if ($page->body) {
    $content .= $page->body;
}

$content .= "</div>"; // .blog_single_item

// Tags & share
if ($page->blog_tags && $page->blog_tags->count()) {
    $content .= "<div class='blog_sing_share'>";
    $content .= "<span>Tags: </span>";
    foreach ($page->blog_tags as $tag) {
        $content .= "<a href='{$pages->get('/blog/')->url}{$tag->name}/' class='tag_link'>{$tag->title}</a> ";
    }
    $content .= "</div>";
}

$content .= "</div>"; // .blog_single_content

// Related posts
$relatedSelector = "template=blog-post, id!={$page->id}, sort=-date, limit=3";
if ($page->blog_categories && $page->blog_categories->count()) {
    $relatedSelector .= ", blog_categories={$page->blog_categories->first()}";
}
$related = $pages->find($relatedSelector);

if ($related->count()) {
    $content .= "<div class='related_posts'>";
    $content .= "<h3 class='widget_title'>Related Posts</h3>";
    foreach ($related as $post) {
        $content .= renderPostCard($post);
    }
    $content .= "</div>";
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

// Schema
$extra_foot .= renderArticleSchema($page);
