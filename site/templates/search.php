<?php namespace ProcessWire;

/**
 * Template: search.php
 * Site search results — Filix inner page layout.
 * Fields: title, body, seo_title, seo_description
 */

$browser_title = 'Search';
$q = $sanitizer->text($input->get('q', 'text'));

// Banner
$hero = renderInnerBanner($page->title, '', 'contact_banner');

// Content — search form and results
$content = "<section class='blog_wrap pd_120'>";
$content .= "<div class='container'>";
$content .= "<div class='row justify-content-center'>";
$content .= "<div class='col-lg-8 col-md-12'>";

$content .= renderBreadcrumbs($page);

// Search form
$content .= "<form action='{$page->url}' method='get' class='search-form wow fadeInUp'>";
$content .= "<div class='row'>";
$content .= "<div class='col-lg-9 col-md-8 col-sm-8 col-12 form-group'>";
$content .= "<input type='search' name='q' value='" . $sanitizer->entities($q) . "' class='form-control' placeholder='Search the site&hellip;' required>";
$content .= "</div>";
$content .= "<div class='col-lg-3 col-md-4 col-sm-4 col-12 form-group'>";
$content .= "<input type='submit' class='sibmit_btn' value='Search'>";
$content .= "</div>";
$content .= "</div>";
$content .= "</form>";

// Search results
if ($q) {
    $q_selector = $sanitizer->selectorValue($q);
    $results = $pages->find("title|body|summary%=$q_selector, template!=admin, template!=blog-rss, template!=blog-tag, has_parent!={$config->adminRootPageID}, limit=20, sort=-modified");

    $browser_title = "Search: {$q}";
    $count = $results->getTotal();
    $content .= "<p class='wow fadeInUp'>";
    $content .= "{$count} result" . ($count !== 1 ? 's' : '') . " for <strong>" . $sanitizer->entities($q) . "</strong>";
    $content .= "</p>";

    if ($results->count()) {
        foreach ($results as $result) {
            $content .= "<div class='search-result-item wow fadeInUp'>";
            $content .= "<h3><a href='{$result->url}'>{$result->title}</a></h3>";

            $snippet = $result->get('summary|');
            if (!$snippet && $result->body) {
                $snippet = truncate(strip_tags($result->body), 200);
            }
            if ($snippet) {
                $content .= "<p>{$snippet}</p>";
            }

            $content .= "<span class='result-url'>{$result->url}</span>";
            $content .= "</div>";
        }
        $content .= renderPagination($results);
    } else {
        $content .= "<p class='wow fadeInUp'>No results found. Try different keywords.</p>";
    }
}

$content .= "</div>"; // .col-lg-8
$content .= "</div>"; // .row
$content .= "</div>"; // .container
$content .= "</section>";

// No sidebar for search
$sidebar = '';
