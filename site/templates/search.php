<?php namespace ProcessWire;

/**
 * Template: search.php
 * Site search results using ProcessWire's built-in selector engine.
 * Fields: title, body, seo_title, seo_description
 */

$browser_title = 'Search';
$content = renderBreadcrumbs($page);

$content .= "<h1>{$page->title}</h1>";

// Search form — also works with HTMX for live results
$q = $sanitizer->text($input->get('q', 'text'));
$content .= "<form action='{$page->url}' method='get' class='search-form'>";
$content .= "<div class='search-input-group'>";
$content .= "<input type='search' name='q' value='" . $sanitizer->entities($q) . "' placeholder='Search the site&hellip;' aria-label='Search' required>";
$content .= "<button type='submit' class='btn'>Search</button>";
$content .= "</div>";
$content .= "</form>";

// Perform search if query provided
if ($q) {
    $q_selector = $sanitizer->selectorValue($q);
    $results = $pages->find("title|body|summary%=$q_selector, template!=admin, template!=blog-rss, template!=blog-tag, has_parent!={$config->adminRootPageID}, limit=20, sort=-modified");

    $browser_title = "Search: {$q}";
    $content .= "<p class='search-meta'>";
    $content .= $results->getTotal() . " result" . ($results->getTotal() !== 1 ? 's' : '') . " for <strong>" . $sanitizer->entities($q) . "</strong>";
    $content .= "</p>";

    if ($results->count()) {
        $content .= "<div class='search-results'>";
        foreach ($results as $result) {
            $content .= "<article class='search-result'>";
            $content .= "<h2><a href='{$result->url}'>{$result->title}</a></h2>";

            // Show a snippet: summary if available, otherwise truncate body
            $snippet = $result->get('summary|');
            if (!$snippet && $result->body) {
                $snippet = truncate(strip_tags($result->body), 200);
            }
            if ($snippet) {
                $content .= "<p>{$snippet}</p>";
            }

            $content .= "<a href='{$result->url}' class='result-url'>{$result->url}</a>";
            $content .= "</article>";
        }
        $content .= "</div>";
        $content .= renderPagination($results);
    } else {
        $content .= "<p>No results found. Try different keywords.</p>";
    }
}
