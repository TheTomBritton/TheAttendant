# Maid of Threads — TODO

## Priority: High

- [x] **Configure Stripe API keys** — Test keys set in `config.php`.
- [x] **Replace userAuthSalt** — Unique 64-character hex string generated and set in `config.php`.
- [x] **Install PW modules** — All installed: TracyDebugger, SeoMaestro, FrontendForms, CroppableImage3, WireMailSmtp, CronjobDatabaseBackup, MarkupSitemap, SessionHandlerDB.
- [x] **Hide policy pages from main nav** — Created `legal-page` template and switched Privacy Policy + Terms & Conditions to use it. Nav query unchanged — `legal-page` simply isn't in the selector.

## Priority: Medium

- [x] **Add product images** — Placeholder images added (featured + 3 gallery per product). Replace with real photography when available.
- [x] **Add blog post images** — Placeholder featured image on introductory blog post. Replace with real photography when available.
- [x] **Contact form** — Fully implemented with FrontendForms (primary) and manual fallback. Email uses `replyTo()` for SMTP compatibility.
- [x] **Security hardening** — Admin URL changed to `/mot-studio/`, HTTPS redirect enabled, security headers added (X-Content-Type-Options, Referrer-Policy, Permissions-Policy).
- [ ] **Test cart flow** — Add a product to cart, test quantity updates, test checkout redirect (will fail until Stripe keys are configured).
- [ ] **Add favicon** — Create and add a favicon to `site/assets/dist/` or as a PW field on the homepage.
- [ ] **Visual refinement** — Review all page templates in the browser and adjust styling, spacing, and layout as needed.

## Priority: Low

- [ ] **Image optimisation** — WebP auto-generation is enabled in config. Test with real product images to verify sizing/quality.
- [ ] **SEO setup** — SeoMaestro is installed but `_main.php` renders `<title>` and `<meta description>` manually. Replace with `$page->seo->render()` to get OG tags, Twitter Cards, and canonical URLs.
- [ ] **XML sitemap** — MarkupSitemap is installed. Configure template exclusions: exclude `admin`, `blog-rss`, `search`, `cart`, `checkout`, `order-confirmation`. Verify `/sitemap.xml` output.
- [ ] **Email templates** — Configure WireMailSmtp with Krystal SMTP credentials. Without this, `wireMail()` falls back to PHP `mail()` which may silently fail on shared hosting.
- [ ] **Stripe webhooks** — Create `stripe-webhook.php` template to handle `checkout.session.completed` events. Essential for production — the success redirect alone is not reliable (customer may close browser). Handles: stock updates, order confirmation emails, refund processing.
- [ ] **Search functionality** — Test the search template with actual content. May need tweaking for product-specific search.
- [ ] **RSS feed** — Verify `/blog-rss/` outputs valid XML with blog posts.
- [ ] **Performance** — Enable PW's built-in template caching on static pages (home, about, shop, categories). Do NOT use ProCache (paid Pro module). Review Lighthouse scores.
- [ ] **Enable ProcessRedirects** — Core PW module, just enable in admin. Needed for 301 redirects when replacing existing site.
- [ ] **Enable ProcessPageClone** — Core PW module, just enable in admin. Makes adding new products faster by cloning existing ones.

## Bugs Fixed

- [x] **Cart totals broken** — `getCartTotals()` expected `$item['price']` / `$item['qty']` but cart stores `product_id => quantity`. Fixed to look up prices from DB.
- [x] **Cart count in header broken** — `_init.php` iterated cart items as arrays, but they're integers. Fixed.
- [x] **Cart AJAX endpoint mismatch** — Alpine store called `?action=count` but cart checked `?json`. Fixed to handle both.
- [x] **Checkout Stripe URLs broken** — `$input->httpUrl(true)` produced double-path URLs. Fixed to use `$page->httpUrl` / `$confirmation_page->httpUrl`.
- [x] **Contact form email rejected** — Used visitor's email as `from()` which SMTP servers reject (SPF mismatch). Fixed to use site email as `from()` + visitor as `replyTo()`.
- [x] **RSS feed broken** — Template requires noPrepend/noAppend but used variables from `_init.php`. Made self-contained.
- [x] **Newsletter form 404** — Footer form posted to non-existent `/subscribe/`. Replaced with contact page link.

## Completed

- [x] **Add products** — 5 embroidery products (kits, PDF patterns, commissions) with placeholder images, full descriptions, prices, SKUs, and stock.
- [x] **Add homepage content** — Hero title, summary, and body content populated.
- [x] **Add blog post** — Introductory "Welcome to Maid of Threads" post with placeholder featured image, categorised as Behind the Scenes.
- [x] **Add blog tags** — 8 tags created (Sustainability, Handmade, Textile Care, Gift Guide, Natural Fibres, Local Makers, Studio Life, Seasonal).
- [x] **Static page content** — About, Contact, Privacy Policy, Terms & Conditions all populated with realistic content.
- [x] **Shop category descriptions** — All 4 categories have summary and body text.
- [x] **Fix field name mismatches** — `product_images` → `product_gallery`, `tags` → `blog_tags` in template files.
- [x] **Add .com domain** — `httpHosts` updated with both `.co.uk` and `.com` variants.

## Deployment Checklist

- [x] Set `$config->debug = false` for production — Environment-aware config already handles this.
- [x] Set `$config->https = true` for production — Already configured.
- [x] Update `httpHosts` with production domain — Both `.co.uk` and `.com` set.
- [x] Change admin URL from default — Set to `/mot-studio/`.
- [x] Enable HTTPS redirect in `.htaccess` — Section 9A uncommented.
- [x] Add security headers — X-Content-Type-Options, Referrer-Policy, Permissions-Policy.
- [ ] Create database on Krystal cPanel and update credentials in `config.php`
- [ ] Export database from Docker and import to Krystal
- [ ] Run `composer install --no-dev` and upload `wire/` directory
- [ ] Run `npm run build` and upload `site/assets/dist/`
- [ ] Upload site files via SFTP
- [ ] Replace Stripe test keys with live keys (or set as server env vars)
- [ ] Configure SMTP credentials in PW admin (WireMailSmtp)
- [ ] Rename admin directory on server to match `/mot-studio/`
- [ ] Set file permissions (755 dirs, 644 files, 755 site/assets/)
- [ ] Test site loads, admin works, forms send, cart functions
- [ ] Run `/deploy-checklist` for full audit
