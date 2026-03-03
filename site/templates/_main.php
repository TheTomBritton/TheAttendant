<?php namespace ProcessWire;

/**
 * _main.php — Auto-appended after every template
 *
 * Filix theme shell: full-screen hamburger menu, hero region,
 * Bootstrap grid content area with optional sidebar, dark footer.
 */

$dist = $config->urls->assets . 'dist/';
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

    <!-- Filix CSS Stack -->
    <link rel="stylesheet" href="<?= $dist ?>css/bootstrap.css">
    <link rel="stylesheet" href="<?= $dist ?>css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= $dist ?>css/elegant-icon-style.css">
    <link rel="stylesheet" href="<?= $dist ?>css/themify-icons.css">
    <link rel="stylesheet" href="<?= $dist ?>css/slick.css">
    <link rel="stylesheet" href="<?= $dist ?>css/slick-theme.css">
    <link rel="stylesheet" href="<?= $dist ?>css/animated.css">
    <link rel="stylesheet" href="<?= $dist ?>css/style.css">
    <link rel="stylesheet" href="<?= $dist ?>css/responsive.css">

    <!-- Sound M8 overrides -->
    <?php
    $cssFile = $config->paths->assets . 'dist/app.css';
    $cssVersion = file_exists($cssFile) ? filemtime($cssFile) : '1';
    ?>
    <link rel="stylesheet" href="<?= $dist ?>app.css?v=<?= $cssVersion ?>">

    <?= $extra_head ?>
</head>
<body class="<?= $body_class ?>">

    <!-- Header -->
    <header class="header">
        <div class="container-fluid">
            <nav class="navbar">
                <!-- Logo -->
                <a href="<?= $home->url ?>" class="logo">
                    <span class="logo_text"><?= $site_name ?></span>
                </a>

                <!-- Hamburger menu trigger -->
                <div class="hamburger">
                    <span class="m_menu">Menu</span>
                    <span class="m_close">Close</span>
                    <div class="bar_icon">
                        <span class="bar bar_1"></span>
                        <span class="bar bar_2"></span>
                        <span class="bar bar_3"></span>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Full-screen overlay menu -->
        <div class="opnen_menu">
            <div class="header_main_menu">
                <ul class="menu_item">
                    <?php foreach (getNavPages() as $item): ?>
                    <li>
                        <a href="<?= $item->url ?>"><?= $item->title ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Menu footer: contact + social -->
            <div class="sub_footer d-flex justify-content-between align-items-center">
                <ul class="footer_social">
                    <li><a href="#"><i class="fa fa-twitter"></i><i class="fa fa-twitter"></i></a></li>
                    <li><a href="#"><i class="fa fa-instagram"></i><i class="fa fa-instagram"></i></a></li>
                    <li><a href="#"><i class="fa fa-youtube-play"></i><i class="fa fa-youtube-play"></i></a></li>
                </ul>
            </div>
        </div>
    </header>

    <?php if ($hero): ?>
    <!-- Hero / Banner -->
    <?= $hero ?>
    <?php endif; ?>

    <!-- Main content -->
    <?php if ($sidebar): ?>
    <section class="blog_wrap">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <?= $content ?>
                </div>
                <div class="col-lg-4">
                    <div class="blog_sidebar">
                        <?= $sidebar ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php else: ?>
        <?= $content ?>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer_content text-center">
            <!-- Logo -->
            <div class="footer_logo">
                <a href="<?= $home->url ?>">
                    <span class="logo_text"><?= $site_name ?></span>
                </a>
            </div>

            <!-- CTA title -->
            <h2 class="footer_title">Let's Explore <span>Sound Together</span></h2>

            <!-- Social links -->
            <ul class="footer_social">
                <li><a href="#"><i class="fa fa-twitter"></i><i class="fa fa-twitter"></i></a></li>
                <li><a href="#"><i class="fa fa-instagram"></i><i class="fa fa-instagram"></i></a></li>
                <li><a href="#"><i class="fa fa-youtube-play"></i><i class="fa fa-youtube-play"></i></a></li>
            </ul>

            <!-- Copyright -->
            <p class="footer_copy_right">&copy; <?= date('Y') ?> <?= $site_name ?>. All rights reserved.</p>
        </div>
    </footer>

    <!-- Back to top -->
    <div class="go_to_top">
        <a href="#"><i class="fa fa-angle-up"></i><i class="fa fa-angle-up"></i></a>
    </div>

    <!-- Scripts -->
    <script src="<?= $dist ?>js/jquery-3.3.1.min.js"></script>
    <script src="<?= $dist ?>js/bootstrap.min.js"></script>
    <script src="<?= $dist ?>js/slick.min.js"></script>
    <script src="<?= $dist ?>js/wow.js"></script>
    <script src="<?= $dist ?>js/main.js"></script>
    <script src="<?= $dist ?>htmx.min.js"></script>

    <?= $extra_foot ?>
</body>
</html>
