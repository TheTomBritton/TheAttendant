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
    $cssFile = $config->paths->templates . 'assets/dist/app.css';
    $cssVersion = file_exists($cssFile) ? filemtime($cssFile) : '1';
    ?>
    <link rel="stylesheet" href="<?= $config->urls->templates ?>assets/dist/app.css?v=<?= $cssVersion ?>">

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
                    <?php foreach ($home->children('include=hidden') as $item): ?>
                    <li>
                        <a href="<?= $item->url ?>"<?= ($page->rootParent->id === $item->id || $page->id === $item->id) ? ' aria-current="page"' : '' ?>>
                            <?= $item->title ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>
    </header>

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
    <script src="<?= $config->urls->templates ?>assets/dist/htmx.min.js" defer></script>
    <?php
    $jsFile = $config->paths->templates . 'assets/dist/app.js';
    if (file_exists($jsFile)):
        $jsVersion = filemtime($jsFile);
    ?>
    <script src="<?= $config->urls->templates ?>assets/dist/app.js?v=<?= $jsVersion ?>" defer></script>
    <?php endif; ?>

    <?= $extra_foot ?>
</body>
</html>
