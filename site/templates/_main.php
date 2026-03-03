<?php namespace ProcessWire;

/**
 * _main.php — Auto-appended after every template
 *
 * Filix theme shell. Templates set region variables
 * ($content, $sidebar, $hero, etc.) and this file outputs them
 * inside the page shell.
 */

$distUrl = $config->urls->assets . 'dist/';
?><!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="<?= $distUrl ?>images/favicon.png" type="image/x-icon">

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

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= $distUrl ?>css/bootstrap.css">

    <!-- Icon CSS -->
    <link rel="stylesheet" href="<?= $distUrl ?>css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= $distUrl ?>css/font-awesome-animation.min.css">
    <link rel="stylesheet" href="<?= $distUrl ?>css/themify-icons.css">
    <link rel="stylesheet" href="<?= $distUrl ?>css/elegant-icon-style.css">

    <!-- Plugin CSS -->
    <link rel="stylesheet" href="<?= $distUrl ?>css/slick.css">
    <link rel="stylesheet" href="<?= $distUrl ?>css/slick-theme.css">
    <link rel="stylesheet" href="<?= $distUrl ?>css/animated.css">

    <!-- Filix theme CSS -->
    <link rel="stylesheet" href="<?= $distUrl ?>css/style.css">
    <link rel="stylesheet" href="<?= $distUrl ?>css/responsive.css">

    <!-- Sound M8 overrides -->
    <?php
    $cssFile = $config->paths->assets . 'src/app.css';
    $cssVersion = file_exists($cssFile) ? filemtime($cssFile) : '1';
    ?>
    <link rel="stylesheet" href="<?= $config->urls->assets ?>src/app.css?v=<?= $cssVersion ?>">

    <?= $extra_head ?>
</head>
<body id="top" class="<?= $body_class ?>">

    <!-- Header -->
    <header class="header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-12">
                    <nav class="navbar">
                        <a class="navbar-brand logo" href="<?= $home->url ?>">
                            <span class="logo_text"><?= $site_name ?></span>
                        </a>
                        <button class="navbar-toggler hamburger" type="button" data-toggle="collapse" data-target="#header_menu">
                            <span class="m_menu">Menu</span>
                            <span class="m_close">Close</span>
                            <span class="bar_icon">
                                <span class="bar bar_1"></span>
                                <span class="bar bar_2"></span>
                                <span class="bar bar_3"></span>
                            </span>
                        </button>
                        <div class="opnen_menu">
                            <div class="header_main_menu">
                                <ul class="menu_item">
                                    <li><a href="<?= $home->url ?>">Home</a></li>
                                    <?php foreach ($home->children() as $item): ?>
                                    <li><a href="<?= $item->url ?>"><?= $item->title ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <div class="sub_footer">
                            <ul class="footer_social text-center">
                                <li>
                                    <a href="#">
                                        <i class="fa fa-facebook"></i>
                                        <i class="fa fa-facebook"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-twitter"></i>
                                        <i class="fa fa-twitter"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-instagram"></i>
                                        <i class="fa fa-instagram"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <?php if ($hero): ?>
    <?= $hero ?>
    <?php endif; ?>

    <?php if ($sidebar): ?>
    <!-- Content with sidebar -->
    <section class="blog_wrap pd_120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
                    <div class="blog_content">
                        <?= $content ?>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                    <div class="blog_sidebar">
                        <?= $sidebar ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php elseif ($content): ?>
    <!-- Content without sidebar -->
    <?= $content ?>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-12">
                    <div class="footer_content">
                        <div class="footer_logo text-center wow fadeInUp">
                            <a href="<?= $home->url ?>"><span class="logo_text"><?= $site_name ?></span></a>
                        </div>
                        <h2 class="footer_title text-center wow fadeInUp">Let's Make <span>Something Great</span> Together</h2>
                        <ul class="footer_social text-center wow fadeInUp">
                            <li>
                                <a href="#">
                                    <i class="fa fa-facebook"></i>
                                    <i class="fa fa-facebook"></i>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fa fa-twitter"></i>
                                    <i class="fa fa-twitter"></i>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fa fa-instagram"></i>
                                    <i class="fa fa-instagram"></i>
                                </a>
                            </li>
                        </ul>
                        <p class="footer_copy_right text-center wow fadeInUp">&copy; <?= date('Y') ?> <?= $site_name ?>. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <div class="go_to_top">
        <a href="#top">
            <i class="fa fa-angle-up"></i>
            <i class="fa fa-angle-up"></i>
        </a>
    </div>

    <!-- Scripts -->
    <script src="<?= $distUrl ?>js/jquery-3.3.1.min.js"></script>
    <script src="<?= $distUrl ?>js/bootstrap.min.js"></script>
    <script src="<?= $distUrl ?>js/isotope.pkgd.min.js"></script>
    <script src="<?= $distUrl ?>js/imagesloaded.pkgd.min.js"></script>
    <script src="<?= $distUrl ?>js/slick.min.js"></script>
    <script src="<?= $distUrl ?>js/wow.js"></script>
    <script src="<?= $distUrl ?>js/parallax-scroll.js"></script>
    <script src="<?= $distUrl ?>js/universal-tilt.js"></script>
    <script src="<?= $distUrl ?>js/main.js"></script>
    <script src="<?= $distUrl ?>htmx.min.js"></script>

    <?= $extra_foot ?>
</body>
</html>
