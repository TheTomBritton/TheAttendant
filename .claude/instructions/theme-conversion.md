# Theme Conversion Guide

How to convert a pre-made HTML template from `themes/` into a ProcessWire site.

## Overview

The `themes/` directory holds purchased or downloaded HTML templates. During `/new-project`, the operator can choose a theme as the design foundation. Claude then converts the static HTML into dynamic PW templates.

## Conversion Steps

### 1. Analyse the Theme

Read every HTML file in the theme directory. Identify:

- **Common shell** — the header, navigation, and footer markup shared across all pages
- **Page types** — each distinct page layout (homepage, inner page, blog listing, blog post, contact, portfolio, etc.)
- **Sections** — repeating content blocks within pages (hero, features, testimonials, pricing, CTA, etc.)
- **Dynamic content areas** — where CMS-managed content should replace static text/images
- **Asset dependencies** — which CSS/JS files are loaded and in what order

### 2. Map to PW Templates

Create a mapping from HTML files to PW template files:

| HTML file | PW template | Notes |
|---|---|---|
| Common header/footer | `_main.php` | Extracted shell with region variables |
| `index.html` | `home.php` | Homepage sections as region content |
| `about.html` | `basic-page.php` | Or a dedicated template if layout differs significantly |
| `blog.html` | `blog-index.php` | Blog listing with pagination |
| `blog-single.html` | `blog-post.php` | Single article |
| `contact.html` | `contact.php` | Form integration |
| `portfolio.html` | `portfolio-index.php` | Gallery/portfolio listing |
| `portfolio-single.html` | `portfolio.php` | Portfolio detail page |

Not every HTML file needs a 1:1 PW template. Variations of the same layout (e.g. `index-2.html`, `index-3.html`) are design options — pick the best one or let the operator choose.

### 3. Extract the Shell (`_main.php`)

From the common markup across all pages, extract:

- `<!DOCTYPE html>` through `<body>` opening (head section)
- Header/navigation block
- Footer block
- Closing `</body></html>`
- All CSS `<link>` tags and JS `<script>` tags

Replace page-specific content areas with PW region variables:

```php
<?= $hero ?>        <!-- Hero/banner section -->
<?= $content ?>     <!-- Main page content -->
<?= $sidebar ?>     <!-- Sidebar if present -->
```

### 4. Convert Navigation

Replace hardcoded nav links with PW page tree queries:

```php
<?php foreach ($home->children() as $item): ?>
<li class="nav-item">
    <a class="nav-link" href="<?= $item->url ?>"><?= $item->title ?></a>
</li>
<?php endforeach; ?>
```

Preserve the theme's nav classes and structure — only replace the hardcoded `<li>` items.

### 5. Handle Static Assets

**CSS and JS files:**
- Copy the theme's CSS/JS into `site/assets/dist/` (they're pre-built, no compilation needed)
- Update `<link>` and `<script>` paths in `_main.php` to use `$config->urls->assets . 'dist/'`
- If the project also uses Tailwind or another build tool, keep the theme CSS separate and load it before custom styles

**Fonts:**
- Copy into `site/assets/dist/fonts/`
- Update `@font-face` paths in the CSS files

**Images:**
- Theme demo images are placeholders — copy to `site/assets/dist/images/` for initial development
- These should NOT be committed to the project branch (add to `.gitignore` if needed)
- Replace with PW image fields where the content is CMS-managed

**SASS/SCSS (if present):**
- Optionally integrate into the build pipeline for customisation
- Or just use the compiled CSS as-is

### 6. Replace Static Content with PW Fields

For each template, identify what content should be CMS-editable:

| Static element | PW field | Field type |
|---|---|---|
| Page heading | `title` | Built-in |
| Body paragraphs | `body` | FieldtypeTextarea (CKEditor) |
| Intro/subtitle | `summary` | FieldtypeTextarea (plain) |
| Hero image | `featured_image` | FieldtypeImage |
| Gallery images | `images` | FieldtypeImage (multi) |
| Section headings | Repeater fields or dedicated fields | Depends on complexity |
| Testimonials | Repeater field | FieldtypeRepeater |
| Team members | Child pages with template | FieldtypePage or children |

**Keep hardcoded** (not CMS fields):
- Structural markup and CSS classes
- Icon classes
- Layout containers
- JavaScript behaviour

### 7. Handle Homepage Sections

Homepages in purchased themes often have many sections (hero, about, services, portfolio, testimonials, CTA, contact, etc.). Approach options:

**Option A: Simplified** — Map key sections to PW fields on the home template. Hardcode section markup, replace text/images with field values. Best for sites that won't change structure.

**Option B: Flexible** — Use a repeater matrix or separate child pages for each section. More complex but allows reordering/toggling sections. Only use if the operator needs this flexibility.

Default to **Option A** unless the operator requests otherwise.

### 8. Preserve Theme Interactivity

Keep the theme's JavaScript functionality working:
- Carousels/sliders (Slick, Owl, Swiper)
- Scroll animations (WOW.js, AOS)
- Parallax effects
- Isotope/Masonry filtering
- Mobile menu toggles
- Form validation (replace with PW's FrontendForms where appropriate)

Ensure scripts load in the correct order — check for jQuery dependencies.

### 9. Generate Exports

After conversion, generate the standard export files:
- `site/install/fields.json` — all fields needed for the converted templates
- `site/install/templates.json` — all templates with field assignments
- `site/install/pages-tree.json` — initial page structure matching the theme's pages

## Common Pitfalls

- **Relative paths** — theme HTML uses relative paths (`assets/css/style.css`). These must become PW-aware paths (`<?= $config->urls->assets ?>dist/css/style.css`).
- **Multiple CSS frameworks** — if the theme uses Bootstrap but the project also has Tailwind, load Bootstrap first and use Tailwind only for custom additions. Avoid class conflicts.
- **Inline styles** — some themes use inline styles for background images. Convert these to use PW image field URLs.
- **Hardcoded URLs** — search for any hardcoded `http://` or `#` links that should become PW page references.
- **Contact forms** — replace the theme's static form/AJAX handler with FrontendForms module integration.
- **Google Fonts** — keep the `<link>` tag in the head, or convert to self-hosted fonts for GDPR compliance.

## Theme Selection in `/new-project`

When a theme is available, the scaffold wizard should:

1. List available themes from `themes/` directory
2. Show which HTML page types each theme includes
3. Let the operator choose a theme or "No theme (build from scratch)"
4. If a theme is chosen, ask which homepage variant to use (if multiple exist)
5. Proceed with conversion as part of the scaffold process
