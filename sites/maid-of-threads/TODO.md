# Maid of Threads — TODO

## Priority: High

- [ ] **Configure Stripe API keys** — Replace `CHANGE_ME` placeholders in `config.php` (lines 93–99) with test keys from Stripe dashboard. Get keys at https://dashboard.stripe.com/test/apikeys
- [ ] **Replace userAuthSalt** — Generate a unique 64-character random string and replace the placeholder in `config.php` (line 62). Use `openssl rand -hex 32` to generate one.
- [ ] **Install PW modules** — Log in to admin, go to Modules > Install for each:
  - TracyDebugger (dev debugging)
  - SeoMaestro (SEO meta fields)
  - FrontendForms (contact form validation)
  - CroppableImage3 (image cropping)
  - WireMailSmtp (email delivery)
  - CronjobDatabaseBackup (automated backups)
  - SessionHandlerDB (core module — enable in admin)
- [ ] **Hide policy pages from main nav** — Privacy Policy and Terms & Conditions show in the nav because they use `basic-page` template. Options:
  - Create a dedicated `legal-page` template (identical to basic-page but excluded from nav query)
  - Or add a `show_in_nav` toggle field to basic-page and filter on it

## Priority: Medium

- [ ] **Add product images** — Upload real product photography via PW admin. Each product needs a featured_image and product_gallery images. Current products have text content but no images.
- [ ] **Add blog post images** — Upload featured images for each blog post via PW admin.
- [ ] **Test cart flow** — Add a product to cart, test quantity updates, test checkout redirect (will fail until Stripe keys are configured).
- [ ] **Add favicon** — Create and add a favicon to `site/assets/dist/` or as a PW field on the homepage.
- [ ] **Contact form** — The contact template currently only renders a placeholder. Wire up FrontendForms module after installation.
- [ ] **Visual refinement** — Review all page templates in the browser and adjust styling, spacing, and layout as needed.

## Priority: Low

- [ ] **Image optimisation** — WebP auto-generation is enabled in config. Test with real product images to verify sizing/quality.
- [ ] **SEO setup** — After SeoMaestro is installed, configure default meta templates and verify Open Graph tags.
- [ ] **XML sitemap** — Install MarkupSitemap module and verify `/sitemap.xml` output.
- [ ] **Email templates** — Configure WireMailSmtp with SMTP credentials for order confirmations and contact form notifications.
- [ ] **Stripe webhooks** — Set up webhook endpoint for order status updates (payment confirmation, refunds).
- [ ] **Search functionality** — Test the search template with actual content. May need tweaking for product-specific search.
- [ ] **RSS feed** — Verify `/blog-rss/` outputs valid XML with blog posts.
- [ ] **Performance** — Enable ProCache or file caching once content is in place. Review Lighthouse scores.
- [ ] **Security hardening** — Before going live: change admin URL, set file permissions, review `.htaccess`, disable debug mode.

## Completed

- [x] **Add products** — 12 products created across 4 categories with full descriptions, prices, SKUs, stock, features, and related product links.
- [x] **Add homepage content** — Hero title, summary, and body content populated.
- [x] **Add blog posts** — 9 posts created across 3 categories (News, Inspiration, Behind the Scenes) with dates Oct 2025–Feb 2026.
- [x] **Add blog tags** — 8 tags created (Sustainability, Handmade, Textile Care, Gift Guide, Natural Fibres, Local Makers, Studio Life, Seasonal).
- [x] **Static page content** — About, Contact, Privacy Policy, Terms & Conditions all populated with realistic content.
- [x] **Shop category descriptions** — All 4 categories have summary and body text.
- [x] **Fix field name mismatches** — `product_images` → `product_gallery`, `tags` → `blog_tags` in template files.

## Deployment Checklist

- [ ] Update production database credentials in `config.php`
- [ ] Update `httpHosts` with production domain
- [ ] Set `$config->debug = false` for production
- [ ] Set `$config->https = true` for production
- [ ] Replace Stripe test keys with live keys
- [ ] Run `/deploy-checklist` for full audit
