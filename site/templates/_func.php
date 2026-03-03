<?php namespace ProcessWire;

/**
 * _func.php — Reusable helper functions
 *
 * Outputs Filix theme-compatible markup (Bootstrap grid, Filix classes).
 * Included by _init.php, available in all templates.
 */

// ──────────────────────────────────────────────
// Images
// ──────────────────────────────────────────────

/**
 * Render a responsive image with srcset and lazy loading
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

// ──────────────────────────────────────────────
// Navigation
// ──────────────────────────────────────────────

/**
 * Get visible navigation pages (top-level children of home)
 */
function getNavPages(): PageArray {
    $home = wire('pages')->get('/');
    // Only visible, non-system pages
    return $home->children();
}

/**
 * Render breadcrumb navigation with schema markup
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

    $items[] = "<li class='active'>{$page->title}</li>";
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

    $out = "<nav aria-label='Breadcrumb' class='breadcrumb_nav'>";
    $out .= "<ol class='breadcrumb'>";
    $out .= implode('', $items);
    $out .= "</ol></nav>";
    $out .= "<script type='application/ld+json'>{$schema}</script>";

    return $out;
}

// ──────────────────────────────────────────────
// Banners
// ──────────────────────────────────────────────

/**
 * Render Filix inner page banner
 */
function renderInnerBanner(string $title, string $subtitle = '', string $extraClass = ''): string {
    $out = "<section class='hero_warp inner_banner {$extraClass}'>";
    $out .= "<div class='container'>";
    $out .= "<div class='banner_content'>";
    $out .= "<h1 class='banner_title'>{$title}</h1>";
    if ($subtitle) {
        $out .= "<p class='banner_para'>{$subtitle}</p>";
    }
    $out .= "</div>";
    $out .= "</div>";
    $out .= "</section>";
    return $out;
}

// ──────────────────────────────────────────────
// Text
// ──────────────────────────────────────────────

/**
 * Truncate text to a given length, respecting word boundaries
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

// ──────────────────────────────────────────────
// Blog
// ──────────────────────────────────────────────

/**
 * Render a blog post card in Filix blog listing style
 */
function renderPostCard(Page $post): string {
    $out = "<div class='blog_single_item wow fadeInUp'>";
    $out .= "<div class='blog_post'>";

    // Post image
    if ($post->featured_image) {
        $thumb = $post->featured_image->size(770, 450);
        $out .= "<div class='post_img'>";
        $out .= "<a href='{$post->url}'>";
        $out .= "<img src='{$thumb->url}' alt='" . wire('sanitizer')->entities($post->title) . "' width='770' height='450' loading='lazy'>";
        $out .= "</a>";
        $out .= "</div>";
    }

    // Post content
    $out .= "<div class='post_content'>";

    // Title
    $out .= "<h3 class='post_title'><a href='{$post->url}'>{$post->title}</a></h3>";

    // Post meta (date, categories)
    $out .= "<ul class='post_info'>";
    $date = date('j F Y', $post->getUnformatted('date'));
    $out .= "<li><span class='post_time'>{$date}</span></li>";

    if ($post->blog_categories && $post->blog_categories->count()) {
        foreach ($post->blog_categories as $cat) {
            $out .= "<li><a href='{$cat->url}'>{$cat->title}</a></li>";
        }
    }
    $out .= "</ul>";

    // Excerpt
    if ($post->summary) {
        $out .= "<p class='post_details'>" . truncate($post->summary, 180) . "</p>";
    }

    // Read more
    $out .= "<a href='{$post->url}' class='read_more'>Read More</a>";

    $out .= "</div>"; // .post_content
    $out .= "</div>"; // .blog_post
    $out .= "</div>"; // .blog_single_item

    return $out;
}

/**
 * Render Article JSON-LD schema
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

    return "<script type='application/ld+json'>" . json_encode($schema, JSON_UNESCAPED_SLASHES) . "</script>";
}

// ──────────────────────────────────────────────
// Blog sidebar
// ──────────────────────────────────────────────

/**
 * Render blog sidebar with Filix widget pattern
 */
function renderBlogSidebar(): string {
    $pages = wire('pages');
    $out = '';

    // Recent posts widget
    $recentPosts = $pages->find("template=blog-post, sort=-date, limit=4");
    if ($recentPosts->count()) {
        $out .= "<div class='widget'>";
        $out .= "<h3 class='widget_title'>Recent Posts</h3>";

        foreach ($recentPosts as $post) {
            $out .= "<div class='media'>";

            if ($post->featured_image) {
                $thumb = $post->featured_image->size(100, 90);
                $out .= "<div class='media-left'>";
                $out .= "<a href='{$post->url}'><img src='{$thumb->url}' alt='" . wire('sanitizer')->entities($post->title) . "' width='100' height='90'></a>";
                $out .= "</div>";
            }

            $out .= "<div class='media-body'>";
            $out .= "<div class='tn_tittle'><a href='{$post->url}'>{$post->title}</a></div>";
            $date = date('j M Y', $post->getUnformatted('date'));
            $out .= "<ul class='recent_post_meta'><li>{$date}</li></ul>";
            $out .= "</div>";

            $out .= "</div>"; // .media
        }

        $out .= "</div>"; // .widget
    }

    // Categories widget
    $categories = $pages->find("template=blog-category, sort=title");
    if ($categories->count()) {
        $out .= "<div class='widget'>";
        $out .= "<h3 class='widget_title'>Categories</h3>";
        $out .= "<ul>";
        foreach ($categories as $cat) {
            $count = $pages->count("template=blog-post, blog_categories={$cat}");
            $out .= "<li><a href='{$cat->url}'>{$cat->title} ({$count})</a></li>";
        }
        $out .= "</ul>";
        $out .= "</div>";
    }

    // Tags widget
    $tags = $pages->find("template=blog-tag, sort=title");
    if ($tags->count()) {
        $out .= "<div class='widget widget_tags'>";
        $out .= "<h3 class='widget_title'>Tags</h3>";
        $out .= "<ul>";
        foreach ($tags as $tag) {
            $out .= "<li><a href='{$pages->get('/blog/')->url}?tag={$tag->name}'>{$tag->title}</a></li>";
        }
        $out .= "</ul>";
        $out .= "</div>";
    }

    return $out;
}

// ──────────────────────────────────────────────
// Pagination
// ──────────────────────────────────────────────

/**
 * Render Filix-styled pagination
 */
function renderPagination($results): string {
    if (!$results->getTotal() || $results->getTotal() <= $results->getLimit()) return '';

    return $results->renderPager([
        'listMarkup' => '<div class="pagination_content"><nav class="navigation"><ul class="pagination">{out}</ul></nav></div>',
        'itemMarkup' => '<li class="{class}">{out}</li>',
        'linkMarkup' => '<a href="{url}">{out}</a>',
        'currentLinkMarkup' => '<a href="{url}" class="active">{out}</a>',
        'currentItemClass' => 'active',
    ]);
}

// ──────────────────────────────────────────────
// Generic
// ──────────────────────────────────────────────

/**
 * Render a generic page card
 */
function renderPageCard(Page $item, bool $showImage = true, bool $showSummary = true): string {
    $out = "<div class='blog_single_item wow fadeInUp'>";
    $out .= "<div class='blog_post'>";

    if ($showImage && $item->featured_image) {
        $thumb = $item->featured_image->size(770, 450);
        $out .= "<div class='post_img'>";
        $out .= "<a href='{$item->url}'>";
        $out .= "<img src='{$thumb->url}' alt='" . wire('sanitizer')->entities($item->title) . "' width='770' height='450' loading='lazy'>";
        $out .= "</a>";
        $out .= "</div>";
    }

    $out .= "<div class='post_content'>";
    $out .= "<h3 class='post_title'><a href='{$item->url}'>{$item->title}</a></h3>";

    if ($showSummary && $item->summary) {
        $out .= "<p class='post_details'>" . truncate($item->summary, 180) . "</p>";
    }

    $out .= "<a href='{$item->url}' class='read_more'>Read More</a>";
    $out .= "</div>";
    $out .= "</div>";
    $out .= "</div>";

    return $out;
}
