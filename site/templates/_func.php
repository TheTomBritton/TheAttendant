<?php namespace ProcessWire;

/**
 * _func.php — Reusable helper functions
 *
 * Included by _init.php, available in all templates.
 * Add project-specific helper functions here.
 */

/**
 * Render a responsive image with srcset and lazy loading
 *
 * @param Pageimage|null $image The image to render
 * @param array $widths Widths to generate for srcset
 * @param string $sizes The sizes attribute value
 * @param bool $lazy Whether to lazy load (false for above-fold images)
 * @return string HTML markup
 */
function renderImage(?Pageimage $image, array $widths = [400, 800, 1200], string $sizes = '100vw', bool $lazy = true): string {
    if (!$image) return '';

    $srcset = [];
    foreach ($widths as $w) {
        $resized = $image->width($w);
        $srcset[] = "{$resized->url} {$w}w";
    }

    $default = $image->width($widths[1] ?? 800);
    $alt = wire('sanitizer')->entities($image->description);
    $loading = $lazy ? " loading='lazy'" : " fetchpriority='high'";

    return "<img src='{$default->url}' srcset='" . implode(', ', $srcset) . "' sizes='{$sizes}' alt='{$alt}' width='{$default->width}' height='{$default->height}'{$loading}>";
}

/**
 * Render breadcrumb navigation with schema markup
 *
 * @param Page $page Current page
 * @return string HTML breadcrumb markup
 */
function renderBreadcrumbs(Page $page): string {
    if ($page->id === wire('pages')->get('/')->id) return '';

    $items = [];
    $schemaItems = [];
    $position = 1;

    foreach ($page->parents as $parent) {
        $items[] = "<li><a href='{$parent->url}'>{$parent->title}</a></li>";
        $schemaItems[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => $parent->title,
            'item' => $parent->httpUrl,
        ];
    }

    $items[] = "<li aria-current='page'>{$page->title}</li>";
    $schemaItems[] = [
        '@type' => 'ListItem',
        'position' => $position,
        'name' => $page->title,
    ];

    $schema = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $schemaItems,
    ], JSON_UNESCAPED_SLASHES);

    $out = "<nav aria-label='Breadcrumb'>";
    $out .= "<ol class='breadcrumbs'>" . implode('', $items) . "</ol>";
    $out .= "</nav>";
    $out .= "<script type='application/ld+json'>{$schema}</script>";

    return $out;
}

/**
 * Truncate text to a given length, respecting word boundaries
 *
 * @param string $text Text to truncate
 * @param int $length Maximum character length
 * @param string $suffix Appended when truncated
 * @return string
 */
function truncate(string $text, int $length = 160, string $suffix = '&hellip;'): string {
    $text = strip_tags($text);
    if (mb_strlen($text) <= $length) return $text;
    $truncated = mb_substr($text, 0, $length);
    $lastSpace = mb_strrpos($truncated, ' ');
    if ($lastSpace !== false) {
        $truncated = mb_substr($truncated, 0, $lastSpace);
    }
    return $truncated . $suffix;
}

/**
 * Get primary navigation pages
 *
 * @return PageArray
 */
function getNavPages(): PageArray {
    return wire('pages')->get('/')->children();
}

/**
 * Render a page card for listings
 *
 * @param Page $item The page to render as a card
 * @param bool $showImage Whether to show the featured image
 * @param bool $showSummary Whether to show the summary
 * @return string HTML markup
 */
function renderPageCard(Page $item, bool $showImage = true, bool $showSummary = true): string {
    $out = "<article class='card'>";

    if ($showImage && $item->featured_image) {
        $thumb = $item->featured_image->size(600, 400);
        $out .= "<a href='{$item->url}' class='card-image'>";
        $out .= "<img src='{$thumb->url}' alt='{$thumb->description}' width='600' height='400' loading='lazy'>";
        $out .= "</a>";
    }

    $out .= "<div class='card-body'>";
    $out .= "<h3><a href='{$item->url}'>{$item->title}</a></h3>";

    if ($showSummary && $item->summary) {
        $out .= "<p>" . truncate($item->summary, 120) . "</p>";
    }

    $out .= "<a href='{$item->url}' class='card-link'>Read more</a>";
    $out .= "</div>";
    $out .= "</article>";

    return $out;
}

/**
 * Render a blog post card for listings
 */
function renderPostCard(Page $post): string {
    $out = "<article class='post-card'>";

    if ($post->featured_image) {
        $thumb = $post->featured_image->size(600, 400);
        $out .= "<a href='{$post->url}' class='post-card-image'>";
        $out .= "<img src='{$thumb->url}' alt='" . wire('sanitizer')->entities($thumb->description) . "' width='600' height='400' loading='lazy'>";
        $out .= "</a>";
    }

    $out .= "<div class='post-card-body'>";

    // Category badges
    if ($post->blog_categories && $post->blog_categories->count()) {
        $out .= "<div class='post-card-categories'>";
        foreach ($post->blog_categories as $cat) {
            $out .= "<a href='{$cat->url}' class='category-badge'>{$cat->title}</a> ";
        }
        $out .= "</div>";
    }

    $out .= "<h2><a href='{$post->url}'>{$post->title}</a></h2>";

    // Date
    $date = date('j F Y', $post->getUnformatted('date'));
    $out .= "<time datetime='" . date('Y-m-d', $post->getUnformatted('date')) . "'>{$date}</time>";

    if ($post->summary) {
        $out .= "<p>" . truncate($post->summary, 150) . "</p>";
    }

    $out .= "<a href='{$post->url}' class='read-more'>Read more</a>";
    $out .= "</div></article>";

    return $out;
}

/**
 * Render Article JSON-LD structured data
 */
function renderArticleSchema(Page $page): string {
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $page->title,
        'description' => $page->get('seo_description|summary|'),
        'datePublished' => date('c', $page->getUnformatted('date')),
        'dateModified' => date('c', $page->modified),
        'url' => $page->httpUrl,
    ];

    if ($page->featured_image) {
        $schema['image'] = $page->featured_image->httpUrl;
    }

    if ($page->blog_author && $page->blog_author->id) {
        $schema['author'] = [
            '@type' => 'Person',
            'name' => $page->blog_author->title,
        ];
    }

    return "<script type='application/ld+json'>" . json_encode($schema, JSON_UNESCAPED_SLASHES) . "</script>";
}

/**
 * Render pagination controls with Tailwind classes
 */
function renderPagination(\ProcessWire\PageArray $items): string {
    return $items->renderPager([
        'nextItemLabel' => 'Next &rarr;',
        'previousItemLabel' => '&larr; Previous',
        'listMarkup' => '<nav aria-label="Pagination"><ul class="pagination">{out}</ul></nav>',
        'itemMarkup' => '<li class="{class}">{out}</li>',
        'linkMarkup' => '<a href="{url}">{out}</a>',
        'currentItemClass' => 'active',
    ]);
}
