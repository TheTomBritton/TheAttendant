# Maid of Threads — TODO

## Priority: High

- [ ] **Add products** — Create sample products in PW admin under Shop > categories (New Arrivals, Accessories, Homeware, Gifts). Each product needs: title, body, price, SKU, featured image, product gallery.
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

## Priority: Medium

- [ ] **Hide policy pages from main nav** — Privacy Policy and Terms & Conditions show in the nav because they use `basic-page` template. Options:
  - Create a dedicated `legal-page` template (identical to basic-page but excluded from nav query)
  - Or add a `show_in_nav` toggle field to basic-page and filter on it
- [ ] **Add homepage content** — The hero currently shows "Home" as the heading. Update the homepage title/body in PW admin to showcase the brand properly.
- [ ] **Add blog posts** — Create a few sample blog posts under Blog to test the blog-index, blog-post, and blog-category templates.
- [ ] **Test cart flow** — Add a product, add to cart, test cart page quantities, test checkout redirect (will fail until Stripe keys are configured).
- [ ] **Add favicon** — Create and add a favicon to `site/assets/dist/` or as a PW field on the homepage.
- [ ] **Contact form** — The contact template currently only renders a placeholder. Wire up FrontendForms module after installation.

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

## Deployment Checklist

- [ ] Update production database credentials in `config.php`
- [ ] Update `httpHosts` with production domain
- [ ] Set `$config->debug = false` for production
- [ ] Set `$config->https = true` for production
- [ ] Replace Stripe test keys with live keys
- [ ] Run `/deploy-checklist` for full audit
