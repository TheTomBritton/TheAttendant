<?php namespace ProcessWire;

/**
 * Template: blog-post.php
 * Single blog article — Filix blog-single-sidebar layout.
 * Fields: title, date, body, summary, featured_image, images,
 *         blog_categories, blog_tags, blog_author,
 *         seo_title, seo_description
 */

$browser_title = $page->get('seo_title|title');
$meta_description = $page->get('seo_description|summary');
$distUrl = $config->urls->assets . 'dist/';

// Banner — blog single style with title and meta
$hero = "<section class='hero_warp inner_banner blog_single_banner'>";
$hero .= "<div class='container'>";
$hero .= "<div class='row d-flex align-items-center'>";
$hero .= "<div class='col-md-12 col-12'>";
$hero .= "<div class='banner_content'>";
$hero .= "<h1 class='banner_title'>{$page->title}</h1>";

// Post meta in banner
$hero .= "<ul class='post_info'>";
if ($page->blog_author && $page->blog_author->id) {
    $hero .= "<li><span class='author'>by {$page->blog_author->title}</span></li>";
}
$date = date('j F Y', $page->getUnformatted('date'));
$hero .= "<li><span class='post_time'><img src='{$distUrl}images/svg/timetable-white.svg' alt='icon'>{$date}</span></li>";
$hero .= "</ul>";

$hero .= "</div>";
$hero .= "</div>";
$hero .= "</div>";
$hero .= "</div>";
$hero .= "</section>";

// Content — blog single
$content = '';
$content .= renderBreadcrumbs($page);

$content .= "<div class='blog_single_item wow fadeInUp'>";

// Body content
if ($page->body) {
    $content .= $page->body;
}

// Featured image within content
if ($page->featured_image) {
    $content .= "<div class='blog_simg_img'>";
    $content .= renderImage($page->featured_image, [600, 900, 1200], '100vw', false);
    $content .= "</div>";
}

// Image gallery
if ($page->images && $page->images->count()) {
    $content .= "<div class='blog_simg_img'>";
    foreach ($page->images as $img) {
        $thumb = $img->width(800);
        $content .= "<img src='{$thumb->url}' alt='" . $sanitizer->entities($img->description) . "' class='img-fluid' style='margin-bottom: 15px;'>";
    }
    $content .= "</div>";
}

$content .= "</div>"; // .blog_single_item

// Tags + categories
if (($page->blog_tags && $page->blog_tags->count()) || ($page->blog_categories && $page->blog_categories->count())) {
    $content .= "<div class='blog_sing_share wow fadeInUp'>";

    if ($page->blog_categories && $page->blog_categories->count()) {
        $content .= "<span class='sing_share'><b>Categories:</b> ";
        $cats = [];
        foreach ($page->blog_categories as $cat) {
            $cats[] = "<a href='{$cat->url}'>{$cat->title}</a>";
        }
        $content .= implode(', ', $cats);
        $content .= "</span> ";
    }

    if ($page->blog_tags && $page->blog_tags->count()) {
        $blogIndex = $pages->get('template=blog-index');
        $content .= "<span class='sing_share'><b>Tags:</b> ";
        $tagLinks = [];
        foreach ($page->blog_tags as $tag) {
            $tagLinks[] = "<a href='{$blogIndex->url}tag/{$tag->name}/'>{$tag->title}</a>";
        }
        $content .= implode(', ', $tagLinks);
        $content .= "</span>";
    }

    $content .= "</div>";
}

// Related posts
if ($page->blog_categories && $page->blog_categories->count()) {
    $related = $pages->find("template=blog-post, blog_categories={$page->blog_categories}, id!={$page->id}, sort=-date, limit=3");
    if ($related->count()) {
        $content .= "<div class='wow fadeInUp' style='margin-top: 40px;'>";
        $content .= "<h3 class='blog_inner_title'>Related Articles</h3>";
        foreach ($related as $rp) {
            $content .= renderPostCard($rp);
        }
        $content .= "</div>";
    }
}

// Sidebar
$sidebar = renderBlogSidebar($page);

// Article structured data
$extra_head = renderArticleSchema($page);
