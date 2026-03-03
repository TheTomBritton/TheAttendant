<?php namespace ProcessWire;

/**
 * Template: search.php
 * Site search — searches blog posts and pages.
 * Uses HTMX for live search suggestions.
 * Fields: title
 */

$q = $sanitizer->text($input->get->q);

// HTMX live search — return partial results
if ($input->get->ajax && $q) {
    $results = $pages->find("template=blog-post, title|body|summary%={$sanitizer->selectorValue($q)}, limit=6, sort=-created");

    if ($results->count()) {
        foreach ($results as $result) {
            echo "<a href='{$result->url}' class='media live_result_item'>";

            if ($result->featured_image) {
                $thumb = $result->featured_image->size(48, 48);
                echo "<div class='media-left'><img src='{$thumb->url}' alt='' width='48' height='48'></div>";
            }

            echo "<div class='media-body'>";
            echo "<div class='tn_tittle'>{$result->title}</div>";
            echo "</div>";
            echo "</a>";
        }
    } else {
        echo "<p class='live_result_empty'>No results found.</p>";
    }
    return; // Skip _main.php
}

// Inner banner
$hero = renderInnerBanner($page->title);

// Full search results
$content = "<div class='contact_form_wrap'>";
$content .= "<div class='container'>";

// Search form
$content .= "<div class='contact_form'>";
$content .= "<form method='get' action='{$page->url}'>";
$content .= "<div class='form-group'>";
$content .= "<input type='search' name='q' value='{$sanitizer->entities($q)}' placeholder='Search articles&hellip;' class='form-control'";
$content .= " hx-get='{$page->url}?ajax=1' hx-trigger='keyup changed delay:300ms' hx-target='#live-results' autofocus>";
$content .= "</div>";
$content .= "<div class='form-group'>";
$content .= "<button type='submit' class='sibmit_btn'>Search</button>";
$content .= "</div>";
$content .= "</form>";

// Live search results dropdown
$content .= "<div id='live-results' class='live_results_wrap'></div>";
$content .= "</div>"; // .contact_form

// Full results
if ($q) {
    $selector = "template=blog-post|basic-page, title|body|summary%={$sanitizer->selectorValue($q)}, limit=20";
    $results = $pages->find($selector);

    $content .= "<p class='search_results_count'>{$results->getTotal()} result" . ($results->getTotal() !== 1 ? 's' : '') . " for &ldquo;{$sanitizer->entities($q)}&rdquo;</p>";

    if ($results->count()) {
        foreach ($results as $result) {
            $content .= "<div class='blog_single_item'>";
            $content .= "<div class='blog_post'>";
            $content .= "<div class='post_content'>";
            $content .= "<h3 class='post_title'><a href='{$result->url}'>{$result->title}</a></h3>";

            $type = match($result->template->name) {
                'blog-post' => 'Blog',
                default => 'Page',
            };
            $content .= "<ul class='post_info'><li>{$type}</li></ul>";

            if ($result->summary) {
                $content .= "<p class='post_details'>" . truncate($result->summary, 200) . "</p>";
            }
            $content .= "</div>"; // .post_content
            $content .= "</div>"; // .blog_post
            $content .= "</div>"; // .blog_single_item
        }

        // Pagination
        $content .= renderPagination($results);
    }
}

$content .= "</div>"; // .container
$content .= "</div>"; // .contact_form_wrap
