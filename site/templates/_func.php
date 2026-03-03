<?php namespace ProcessWire;

/**
 * _func.php — Reusable helper functions
 *
 * Included by _init.php, available in all templates.
 * Outputs Filix theme-compatible markup.
 */

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
    $out .= "<ol>" . implode('', $items) . "</ol>";
    $out .= "</nav>";
    $out .= "<script type='application/ld+json'>{$schema}</script>";

    return $out;
}

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

/**
 * Get primary navigation pages
 */
function getNavPages(): PageArray {
    return wire('pages')->get('/')->children();
}

/**
 * Render a blog post card — Filix blog_post markup
 */
function renderPostCard(Page $post): string {
    $distUrl = wire('config')->urls->assets . 'dist/';

    $out = "<div class='blog_single_item wow fadeInUp'>";
    $out .= "<div class='blog_post'>";

    // Post image
    if ($post->featured_image) {
        $thumb = $post->featured_image->width(800);
        $out .= "<div class='post_img'>";
        $out .= "<a href='{$post->url}'><img src='{$thumb->url}' alt='" . wire('sanitizer')->entities($thumb->description) . "'></a>";
        $out .= "</div>";
    }

    $out .= "<div class='post_content'>";

    // Post info (author + date)
    $date = date('j F Y', $post->getUnformatted('date'));
    $out .= "<ul class='post_info'>";
    if ($post->blog_author && $post->blog_author->id) {
        $out .= "<li><span class='author'>by {$post->blog_author->title}</span></li>";
    }
    $out .= "<li class='float-right'><span class='post_time'><img src='{$distUrl}images/svg/timetable.svg' alt='icon'>{$date}</span></li>";
    $out .= "</ul>";

    // Title
    $out .= "<h3 class='post_title'><a href='{$post->url}'>{$post->title}</a></h3>";

    // Summary
    if ($post->summary) {
        $out .= "<p class='post_details'>" . truncate($post->summary, 200) . "</p>";
    }

    $out .= "<a href='{$post->url}' class='read_more'>Explore</a>";
    $out .= "</div>"; // .post_content
    $out .= "</div>"; // .blog_post
    $out .= "</div>"; // .blog_single_item

    return $out;
}

/**
 * Render a page card — Filix blog_post style for generic pages
 */
function renderPageCard(Page $item, bool $showImage = true, bool $showSummary = true): string {
    $out = "<div class='blog_single_item wow fadeInUp'>";
    $out .= "<div class='blog_post'>";

    if ($showImage && $item->featured_image) {
        $thumb = $item->featured_image->width(800);
        $out .= "<div class='post_img'>";
        $out .= "<a href='{$item->url}'><img src='{$thumb->url}' alt='" . wire('sanitizer')->entities($thumb->description) . "'></a>";
        $out .= "</div>";
    }

    $out .= "<div class='post_content'>";
    $out .= "<h3 class='post_title'><a href='{$item->url}'>{$item->title}</a></h3>";

    if ($showSummary && $item->summary) {
        $out .= "<p class='post_details'>" . truncate($item->summary, 150) . "</p>";
    }

    $out .= "<a href='{$item->url}' class='read_more'>Explore</a>";
    $out .= "</div>";
    $out .= "</div>";
    $out .= "</div>";

    return $out;
}

/**
 * Render pagination — Filix pagination_content style
 */
function renderPagination(\ProcessWire\PageArray $items): string {
    return $items->renderPager([
        'nextItemLabel' => '<i class="arrow_right"></i>',
        'previousItemLabel' => '<i class="arrow_left"></i>',
        'listMarkup' => '<div class="pagination_content wow fadeInUp"><nav class="navigation"><ul class="pagination text-center">{out}</ul></nav></div>',
        'itemMarkup' => '<li class="{class}">{out}</li>',
        'linkMarkup' => '<a href="{url}">{out}</a>',
        'currentItemClass' => 'active',
    ]);
}

/**
 * Render sidebar widgets — categories, recent posts, tags
 */
function renderBlogSidebar(Page $currentPage = null): string {
    $pages = wire('pages');
    $distUrl = wire('config')->urls->assets . 'dist/';
    $sidebar = '';

    // Recent posts widget
    $recentPosts = $pages->find("template=blog-post, sort=-date, limit=3");
    if ($recentPosts->count()) {
        $sidebar .= "<div class='widget sidebar-widget widget_recent_post wow fadeInUp'>";
        $sidebar .= "<h2 class='widget_title'>Recent Posts</h2>";
        foreach ($recentPosts as $rp) {
            $sidebar .= "<div class='media'>";
            if ($rp->featured_image) {
                $rpThumb = $rp->featured_image->size(80, 80);
                $sidebar .= "<div class='media-left'><a href='{$rp->url}'><img class='media-object' src='{$rpThumb->url}' alt='" . wire('sanitizer')->entities($rpThumb->description) . "'></a></div>";
            }
            $sidebar .= "<div class='media-body'>";
            $sidebar .= "<h6 class='tn_tittle'><a href='{$rp->url}'>{$rp->title}</a></h6>";
            $rpDate = date('j F Y', $rp->getUnformatted('date'));
            $sidebar .= "<ul class='recent_post_meta'><li><a href='{$rp->url}'>{$rpDate}</a></li></ul>";
            $sidebar .= "</div>";
            $sidebar .= "</div>";
        }
        $sidebar .= "</div>";
    }

    // Categories widget
    $categories = $pages->find("template=blog-category, sort=title");
    if ($categories->count()) {
        $sidebar .= "<div class='widget sidebar-widget widget_tags wow fadeInUp'>";
        $sidebar .= "<h2 class='widget_title'>Categories</h2>";
        $sidebar .= "<ul>";
        foreach ($categories as $cat) {
            $postCount = $pages->count("template=blog-post, blog_categories=$cat");
            $sidebar .= "<li><a href='{$cat->url}'>{$cat->title} <span>({$postCount})</span></a></li>";
        }
        $sidebar .= "</ul>";
        $sidebar .= "</div>";
    }

    // Tags widget
    $blogIndex = $pages->get('template=blog-index');
    $tags = $pages->find("template=blog-tag, sort=title");
    if ($tags->count() && $blogIndex->id) {
        $sidebar .= "<div class='widget sidebar-widget widget_tags wow fadeInUp'>";
        $sidebar .= "<h2 class='widget_title'>Tags</h2>";
        $sidebar .= "<ul>";
        foreach ($tags as $tag) {
            $sidebar .= "<li><a href='{$blogIndex->url}tag/{$tag->name}/'>{$tag->title}</a></li>";
        }
        $sidebar .= "</ul>";
        $sidebar .= "</div>";
    }

    return $sidebar;
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
 * Render the inner page banner — Filix hero_warp inner_banner
 */
function renderInnerBanner(string $title, string $subtitle = '', string $extraClass = ''): string {
    $classes = "hero_warp inner_banner";
    if ($extraClass) $classes .= " {$extraClass}";

    $out = "<section class='{$classes}'>";
    $out .= "<div class='container'>";
    $out .= "<div class='row d-flex align-items-center'>";
    $out .= "<div class='col-md-12 col-12'>";
    $out .= "<div class='banner_content'>";
    $out .= "<h1 class='banner_title'>{$title}</h1>";
    if ($subtitle) {
        $out .= "<p class='banner_para wow fadeInUp'>{$subtitle}</p>";
    }
    $out .= "</div>";
    $out .= "</div>";
    $out .= "</div>";
    $out .= "</div>";
    $out .= "</section>";

    return $out;
}
