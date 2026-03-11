# Maid of Threads — TODO

## Priority: High

- [x] **Configure Stripe API keys** — Test keys set in `config.php`.
- [x] **Replace userAuthSalt** — Unique 64-character hex string generated and set in `config.php`.
- [x] **Install PW modules** — All installed: TracyDebugger, SeoMaestro, FrontendForms, CroppableImage3, WireMailSmtp, CronjobDatabaseBackup, MarkupSitemap, SessionHandlerDB.
- [x] **Hide policy pages from main nav** — Created `legal-page` template and switched Privacy Policy + Terms & Conditions to use it. Nav query unchanged — `legal-page` simply isn't in the selector.

## Priority: Medium

- [x] **Add product images** — Placeholder images added (featured + 3 gallery per product). Replace with real photography when available.
- [x] **Add blog post images** — Placeholder featured image on introductory blog post. Replace with real photography when available.
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

- [x] **Add products** — 5 embroidery products (kits, PDF patterns, commissions) with placeholder images, full descriptions, prices, SKUs, and stock.
- [x] **Add homepage content** — Hero title, summary, and body content populated.
- [x] **Add blog post** — Introductory "Welcome to Maid of Threads" post with placeholder featured image, categorised as Behind the Scenes.
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
