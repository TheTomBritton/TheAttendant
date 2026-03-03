<?php namespace ProcessWire;

/**
 * _main.php — Auto-appended after every template
 *
 * This is the main HTML wrapper. Templates set region variables
 * ($content, $sidebar, $hero, etc.) and this file outputs them
 * inside the page shell.
 */
?><!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $sanitizer->entities($browser_title) ?><?= $page->id !== $home->id ? " | {$site_name}" : '' ?></title>
    <meta name="description" content="<?= $sanitizer->entities($meta_description) ?>">
    <link rel="canonical" href="<?= $page->httpUrl ?>">

    <!-- Open Graph -->
    <meta property="og:title" content="<?= $sanitizer->entities($browser_title) ?>">
    <meta property="og:description" content="<?= $sanitizer->entities($meta_description) ?>">
    <meta property="og:url" content="<?= $page->httpUrl ?>">
    <meta property="og:type" content="website">
    <?php if ($page->featured_image): ?>
    <meta property="og:image" content="<?= $page->featured_image->width(1200)->httpUrl ?>">
    <?php endif; ?>

    <!-- RSS feed -->
    <?php $blogRss = $pages->get('template=blog-rss'); ?>
    <?php if ($blogRss->id): ?>
    <link rel="alternate" type="application/rss+xml" title="<?= $site_name ?> Blog" href="<?= $blogRss->httpUrl ?>">
    <?php endif; ?>

    <!-- Styles -->
    <?php
    $cssFile = $config->paths->assets . 'dist/app.css';
    $cssVersion = file_exists($cssFile) ? filemtime($cssFile) : '1';
    ?>
    <link rel="stylesheet" href="<?= $config->urls->assets ?>dist/app.css?v=<?= $cssVersion ?>">

    <?= $extra_head ?>
</head>
<body class="<?= $body_class ?>">

    <!-- Skip navigation -->
    <a href="#content" class="sr-only focus:not-sr-only">Skip to content</a>

    <!-- Site header -->
    <header role="banner">
        <div class="container">
            <a href="<?= $home->url ?>" class="site-logo" aria-label="<?= $site_name ?> — Home">
                <?= $site_name ?>
            </a>

            <nav aria-label="Main navigation">
                <ul>
                    <?php foreach ($home->children() as $item): ?>
                    <li>
                        <a href="<?= $item->url ?>"<?= ($page->rootParent->id === $item->id || $page->id === $item->id) ? ' aria-current="page"' : '' ?>>
                            <?= $item->title ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <?php $searchPage = $pages->get('template=search'); ?>
            <?php if ($searchPage->id): ?>
            <a href="<?= $searchPage->url ?>" class="nav-search-link" aria-label="Search">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </a>
            <?php endif; ?>

            <button class="mobile-menu-btn" aria-label="Toggle menu" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>

    <!-- Mobile navigation -->
    <nav class="mobile-nav" id="mobile-nav" aria-label="Mobile navigation">
        <ul>
            <?php foreach ($home->children() as $item): ?>
            <li>
                <a href="<?= $item->url ?>"<?= ($page->rootParent->id === $item->id || $page->id === $item->id) ? ' aria-current="page"' : '' ?>>
                    <?= $item->title ?>
                </a>
            </li>
            <?php endforeach; ?>
            <?php if ($searchPage->id): ?>
            <li>
                <a href="<?= $searchPage->url ?>">Search</a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>

    <?php if ($hero): ?>
    <!-- Hero / Banner -->
    <?= $hero ?>
    <?php endif; ?>

    <!-- Main content -->
    <main id="content">
        <div class="container">
            <?php if ($sidebar): ?>
            <div class="layout-with-sidebar">
                <div class="main-content">
                    <?= $content ?>
                </div>
                <aside role="complementary">
                    <?= $sidebar ?>
                </aside>
            </div>
            <?php else: ?>
                <?= $content ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Site footer -->
    <footer role="contentinfo">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= $site_name ?>. All rights reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="<?= $config->urls->assets ?>dist/htmx.min.js" defer></script>
    <?php
    $jsFile = $config->paths->assets . 'dist/app.js';
    if (file_exists($jsFile)):
        $jsVersion = filemtime($jsFile);
    ?>
    <script src="<?= $config->urls->assets ?>dist/app.js?v=<?= $jsVersion ?>" defer></script>
    <?php endif; ?>

    <!-- Mobile menu toggle -->
    <script>
    (function() {
        var btn = document.querySelector('.mobile-menu-btn');
        var nav = document.getElementById('mobile-nav');
        if (!btn || !nav) return;
        btn.addEventListener('click', function() {
            var open = nav.classList.toggle('is-open');
            btn.setAttribute('aria-expanded', open);
            document.body.style.overflow = open ? 'hidden' : '';
        });
        // Close on link click
        nav.querySelectorAll('a').forEach(function(a) {
            a.addEventListener('click', function() {
                nav.classList.remove('is-open');
                btn.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            });
        });
    })();
    </script>

    <?= $extra_foot ?>
</body>
</html>
