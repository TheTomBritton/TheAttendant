<?php namespace ProcessWire;

/**
 * Template: blog-post.php
 * Single blog article with related posts and structured data.
 * Fields: title, date, body, summary, featured_image, images,
 *         blog_categories, blog_tags, blog_author,
 *         seo_title, seo_description
 */

$browser_title = $page->get('seo_title|title');
$meta_description = $page->get('seo_description|summary');

$content = renderBreadcrumbs($page);

// Article
$content .= "<article class='blog-post'>";

// Header
$content .= "<header class='post-header'>";
$content .= "<h1>{$page->title}</h1>";

// Meta line: date, categories, author
$content .= "<div class='post-meta'>";

$date = date('j F Y', $page->getUnformatted('date'));
$content .= "<time datetime='" . date('Y-m-d', $page->getUnformatted('date')) . "'>{$date}</time>";

if ($page->blog_author && $page->blog_author->id) {
    $content .= " <span class='separator'>·</span> ";
    $content .= "<span class='author'>By {$page->blog_author->title}</span>";
}

if ($page->blog_categories && $page->blog_categories->count()) {
    $content .= " <span class='separator'>·</span> ";
    $cats = [];
    foreach ($page->blog_categories as $cat) {
        $cats[] = "<a href='{$cat->url}'>{$cat->title}</a>";
    }
    $content .= implode(', ', $cats);
}

$content .= "</div>"; // .post-meta
$content .= "</header>";

// Featured image (above the fold — no lazy loading)
if ($page->featured_image) {
    $content .= "<figure class='post-featured-image'>";
    $content .= renderImage($page->featured_image, [600, 900, 1200], '100vw', false);
    if ($page->featured_image->description) {
        $content .= "<figcaption>{$page->featured_image->description}</figcaption>";
    }
    $content .= "</figure>";
}

// Post body
$content .= "<div class='post-content'>{$page->body}</div>";

// Image gallery (if any additional images)
if ($page->images && $page->images->count()) {
    $content .= "<section class='post-gallery'>";
    $content .= "<h2>Gallery</h2>";
    $content .= "<div class='gallery-grid'>";
    foreach ($page->images as $img) {
        $thumb = $img->size(400, 300);
        $content .= "<figure>";
        $content .= "<a href='{$img->width(1600)->url}'>";
        $content .= "<img src='{$thumb->url}' alt='" . $sanitizer->entities($img->description) . "' width='400' height='300' loading='lazy'>";
        $content .= "</a>";
        if ($img->description) {
            $content .= "<figcaption>{$img->description}</figcaption>";
        }
        $content .= "</figure>";
    }
    $content .= "</div>";
    $content .= "</section>";
}

// Tags
if ($page->blog_tags && $page->blog_tags->count()) {
    $blogIndex = $pages->get('template=blog-index');
    $content .= "<footer class='post-tags'>";
    $content .= "<p>Tagged: ";
    $tagLinks = [];
    foreach ($page->blog_tags as $tag) {
        $tagLinks[] = "<a href='{$blogIndex->url}tag/{$tag->name}/'>{$tag->title}</a>";
    }
    $content .= implode(', ', $tagLinks);
    $content .= "</p></footer>";
}

$content .= "</article>";

// Related posts (same category)
if ($page->blog_categories && $page->blog_categories->count()) {
    $related = $pages->find("template=blog-post, blog_categories={$page->blog_categories}, id!={$page->id}, sort=-date, limit=3");
    if ($related->count()) {
        $content .= "<section class='related-posts'>";
        $content .= "<h2>Related Articles</h2>";
        $content .= "<div class='posts-grid'>";
        foreach ($related as $post) {
            $content .= renderPostCard($post);
        }
        $content .= "</div></section>";
    }
}

// Article structured data
$extra_head = renderArticleSchema($page);
