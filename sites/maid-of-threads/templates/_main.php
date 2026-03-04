<?php namespace ProcessWire;

/**
 * _main.php — HTML shell wrapper (delayed output)
 *
 * Receives variables set by individual templates:
 *   $content   — main page content (required)
 *   $hero      — optional hero section markup
 *   $sidebar   — optional sidebar markup
 *   $extra_head — optional extra <head> content (styles, meta, etc.)
 *   $extra_foot — optional extra scripts before </body>
 *
 * @var string $content
 * @var string|null $hero
 * @var string|null $sidebar
 * @var string|null $extra_head
 * @var string|null $extra_foot
 */

// Ensure optional vars have defaults
$hero       = $hero ?? '';
$sidebar    = $sidebar ?? '';
$extra_head = $extra_head ?? '';
$extra_foot = $extra_foot ?? '';

// Commonly referenced pages
$home      = $pages->get('/');
// $site_name is set in _init.php
$shop_page = $pages->get('/shop/');
$cart_page = $pages->get('/cart/');
$blog_page = $pages->get('/blog/');

// Asset paths with cache busting
$dist_url  = $config->urls->assets . 'dist/';
$dist_path = $config->paths->assets . 'dist/';

$css_file  = 'app.css';
$js_file   = 'app.js';
$css_ver   = file_exists($dist_path . $css_file) ? filemtime($dist_path . $css_file) : time();
$js_ver    = file_exists($dist_path . $js_file)  ? filemtime($dist_path . $js_file)  : time();

// Free shipping threshold display
$free_shipping_threshold = isset($config->shopFreeShippingThreshold)
    ? formatPrice($config->shopFreeShippingThreshold)
    : '£50.00';

?><!doctype html>
<html lang="en-GB" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $page->title ?> — <?= $site_name ?></title>
    <meta name="description" content="<?= $page->summary ?: $home->summary ?>">

    <!-- Tailwind / App CSS -->
    <link rel="stylesheet" href="<?= $dist_url . $css_file ?>?v=<?= $css_ver ?>">

    <!-- Alpine.js (defer so it initialises after DOM parse) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- HTMX -->
    <script src="https://unpkg.com/htmx.org@1.9.12" integrity="sha384-ujb1lZYygJmzgSwoxRggbCHcjc0rB2XoQrxeTUQyRjrOnlCoYta87iKBWq3EsdM2" crossorigin="anonymous"></script>

    <!-- Alpine global store for cart -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('cart', {
                count: 0,
                async refresh() {
                    try {
                        const res = await fetch('/cart/?action=count', {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await res.json();
                        this.count = data.count || 0;
                    } catch (e) {
                        // Silently fail — cart count is non-critical
                    }
                },
                init() {
                    this.refresh();
                }
            });
        });
    </script>

    <?= $extra_head ?>
</head>
<body class="flex min-h-screen flex-col bg-white text-stone-800 antialiased"
      x-data
      @cart-updated.window="$store.cart.refresh()">

    <!-- ═══════════════════════════════════════════
         Announcement Bar
         ═══════════════════════════════════════════ -->
    <div class="bg-brand-700 text-center text-sm font-medium text-white py-2 px-4">
        Free delivery on orders over <?= $free_shipping_threshold ?>
    </div>

    <!-- ═══════════════════════════════════════════
         Header
         ═══════════════════════════════════════════ -->
    <header class="sticky top-0 z-50 border-b border-stone-200 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80"
            x-data="mobileMenu">

        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">

            <!-- Logo / Site Name -->
            <a href="<?= $home->url ?>" class="font-display text-2xl font-bold tracking-tight text-brand-700 hover:text-brand-800 transition-colors">
                <?= $site_name ?>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden items-center gap-8 lg:flex" aria-label="Main navigation">
                <?php
                // Only show visible, published top-level pages (exclude utility/system pages)
                $nav_templates = 'basic-page|shop|blog-index|contact';
                foreach ($home->children("template=$nav_templates, sort=sort") as $child): ?>
                    <a href="<?= $child->url ?>"
                       class="text-sm font-medium text-stone-600 transition-colors hover:text-brand-700<?= $child->id === $page->rootParent->id ? ' text-brand-700' : '' ?>">
                        <?= $child->title ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <!-- Icon Actions -->
            <div class="flex items-center gap-4">

                <!-- Search -->
                <a href="/search/" class="text-stone-500 transition-colors hover:text-brand-700" aria-label="Search">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </a>

                <!-- Cart -->
                <a href="<?= $cart_page->id ? $cart_page->url : '/cart/' ?>" class="relative text-stone-500 transition-colors hover:text-brand-700" aria-label="Shopping cart">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    <!-- Cart count badge -->
                    <span x-show="$store.cart.count > 0"
                          x-text="$store.cart.count"
                          x-transition
                          x-cloak
                          class="absolute -right-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full bg-brand-600 text-xs font-bold text-white">
                    </span>
                </a>

                <!-- Mobile hamburger -->
                <button @click="open = !open"
                        class="text-stone-500 transition-colors hover:text-brand-700 lg:hidden"
                        :aria-expanded="open"
                        aria-label="Toggle menu">
                    <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg x-show="open" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <nav x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             x-cloak
             class="border-t border-stone-200 bg-white px-4 pb-6 pt-4 lg:hidden"
             aria-label="Mobile navigation">
            <div class="flex flex-col gap-4">
                <?php foreach ($home->children("template=$nav_templates, sort=sort") as $child): ?>
                    <a href="<?= $child->url ?>"
                       @click="open = false"
                       class="text-base font-medium text-stone-700 transition-colors hover:text-brand-700<?= $child->id === $page->rootParent->id ? ' text-brand-700' : '' ?>">
                        <?= $child->title ?>
                    </a>
                <?php endforeach; ?>
                <a href="/search/"
                   @click="open = false"
                   class="text-base font-medium text-stone-700 transition-colors hover:text-brand-700">
                    Search
                </a>
            </div>
        </nav>
    </header>

    <!-- ═══════════════════════════════════════════
         Hero Region
         ═══════════════════════════════════════════ -->
    <?php if ($hero): ?>
        <?= $hero ?>
    <?php endif; ?>

    <!-- ═══════════════════════════════════════════
         Main Content
         ═══════════════════════════════════════════ -->
    <main class="flex-1">
        <?php if ($sidebar): ?>
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:px-6 lg:grid-cols-4 lg:px-8">
                <div class="lg:col-span-3">
                    <?= $content ?>
                </div>
                <aside class="lg:col-span-1">
                    <?= $sidebar ?>
                </aside>
            </div>
        <?php else: ?>
            <?= $content ?>
        <?php endif; ?>
    </main>

    <!-- ═══════════════════════════════════════════
         Footer
         ═══════════════════════════════════════════ -->
    <footer class="border-t border-stone-200 bg-stone-50">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">

                <!-- Col 1: Site Info -->
                <div>
                    <h3 class="font-display text-lg font-bold text-brand-700"><?= $site_name ?></h3>
                    <?php if ($home->summary): ?>
                        <p class="mt-3 text-sm leading-relaxed text-stone-600"><?= $home->summary ?></p>
                    <?php endif; ?>
                </div>

                <!-- Col 2: Shop Categories -->
                <div>
                    <h4 class="text-sm font-semibold uppercase tracking-wider text-stone-900">Shop</h4>
                    <ul class="mt-3 space-y-2">
                        <?php if ($shop_page->id):
                            foreach ($shop_page->children as $category): ?>
                                <li>
                                    <a href="<?= $category->url ?>" class="text-sm text-stone-600 transition-colors hover:text-brand-700">
                                        <?= $category->title ?>
                                    </a>
                                </li>
                            <?php endforeach;
                        endif; ?>
                    </ul>
                </div>

                <!-- Col 3: Info Links -->
                <div>
                    <h4 class="text-sm font-semibold uppercase tracking-wider text-stone-900">Information</h4>
                    <ul class="mt-3 space-y-2">
                        <?php
                        $info_links = [
                            '/about/'              => 'About Us',
                            '/contact/'            => 'Contact',
                            '/privacy-policy/'     => 'Privacy Policy',
                            '/terms-and-conditions/' => 'Terms & Conditions',
                        ];
                        foreach ($info_links as $url => $label):
                            $link_page = $pages->get($url);
                            if ($link_page->id): ?>
                                <li>
                                    <a href="<?= $link_page->url ?>" class="text-sm text-stone-600 transition-colors hover:text-brand-700">
                                        <?= $label ?>
                                    </a>
                                </li>
                            <?php endif;
                        endforeach; ?>
                    </ul>
                </div>

                <!-- Col 4: Newsletter / Contact -->
                <div>
                    <h4 class="text-sm font-semibold uppercase tracking-wider text-stone-900">Stay in Touch</h4>
                    <p class="mt-3 text-sm text-stone-600">
                        Sign up for updates on new arrivals and exclusive offers.
                    </p>
                    <form class="mt-4 flex gap-2" method="post" action="/subscribe/">
                        <input type="email"
                               name="email"
                               placeholder="Your email"
                               required
                               class="min-w-0 flex-1 rounded-md border border-stone-300 px-3 py-2 text-sm placeholder:text-stone-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                        <button type="submit"
                                class="rounded-md bg-brand-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                            Join
                        </button>
                    </form>
                </div>
            </div>

            <!-- Bottom bar -->
            <div class="mt-12 border-t border-stone-200 pt-6 text-center text-xs text-stone-500">
                <p>&copy; <?= date('Y') ?> <?= $site_name ?>. All rights reserved.</p>
                <p class="mt-1">All prices include VAT where applicable.</p>
            </div>
        </div>
    </footer>

    <!-- App JS with cache busting -->
    <script src="<?= $dist_url . $js_file ?>?v=<?= $js_ver ?>"></script>

    <!-- Alpine mobileMenu component -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('mobileMenu', () => ({
                open: false,
                init() {
                    // Close mobile menu on window resize past breakpoint
                    window.addEventListener('resize', () => {
                        if (window.innerWidth >= 1024) this.open = false;
                    });
                }
            }));
        });
    </script>

    <?= $extra_foot ?>
</body>
</html>
