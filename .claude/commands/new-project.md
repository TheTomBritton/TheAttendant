# /new-project — Full Project Scaffold Wizard

## Purpose
Scaffold a new ProcessWire website from this template repository. Gather requirements, suggest the right stack, install dependencies, create templates, define fields, and prepare the local dev environment.

## Workflow

### Step 1: Gather Requirements
Ask the operator these questions (all at once, don't drip-feed):

1. **What is the site for?** (e.g. business brochure, portfolio, blog, online shop, directory, membership site)
2. **Client/project name?** (used for naming, Docker container, database, etc.)
3. **Roughly how many pages?** (helps determine template complexity)
4. **Any specific features needed?** (contact forms, galleries, search, maps, events, etc.)
5. **Is there an existing design/brand to work from, or building from scratch?**
6. **Any preference on CSS framework, or should I recommend one?**

### Step 2: Theme Selection
Check the `themes/` directory for available pre-made HTML templates:

```bash
ls themes/
```

If themes are available:
1. List each theme with a brief description of what page types it includes (scan the HTML files)
2. Show a summary, e.g.: "**filix_full** — Portfolio/agency theme with homepage (5 variants), blog, blog single, contact, portfolio"
3. Ask: "Would you like to use one of these themes as the design foundation, or build from scratch?"
4. If a theme is chosen and it has multiple homepage variants, ask which one to use

If no themes are available, skip to Step 3.

### Step 3: Recommend Stack
Based on answers, consult these instruction files and propose a stack:

- Read `.claude/instructions/frontend-stack.md` — recommend CSS framework with reasoning
- Read `.claude/instructions/module-recommendations.md` — list modules to install with brief justification for each
- If ecommerce: read `.claude/instructions/ecommerce-guide.md`
- If blog/news: read `.claude/instructions/blog-setup.md`
- If a theme was chosen: read `.claude/instructions/theme-conversion.md` — note the theme's existing CSS framework (Bootstrap, etc.) and factor that into the stack recommendation. Don't replace the theme's framework — build on top of it.

Present the recommendation as a clear summary and wait for approval before proceeding.

### Step 4: Configure Project
Once approved:

1. **Create a project branch** — never scaffold directly on `main`. The `main` branch is the clean starter template.
   ```bash
   git checkout -b <project-name>
   ```
2. Update `composer.json` with the project name
3. Update `package.json` with the project name and any additional frontend dependencies
4. Update `docker/.env.example` → copy to `docker/.env` with project-specific values:
   - `PROJECT_NAME` — sanitised project name
   - `DB_NAME` — database name
   - `DB_USER` / `DB_PASS` — dev credentials
   - `PW_ADMIN_USER` / `PW_ADMIN_PASS` — default admin credentials for dev
5. If a different CSS framework was chosen, swap out Tailwind config files accordingly
6. Run `npm install` (on host machine — creates `node_modules/` and copies any vendored JS to `site/assets/dist/`)
7. Do NOT run `composer install` yet — that happens inside Docker after containers are up

### Step 5: Scaffold Templates

**If a theme was chosen:**
Follow the conversion process in `.claude/instructions/theme-conversion.md`:
1. Analyse all HTML files in the theme directory
2. Extract the common shell (header, nav, footer) → `_main.php`
3. Convert each page type into a PW template file
4. Copy theme assets (CSS, JS, fonts) into `site/assets/dist/`
5. Replace static content with PW field calls
6. Convert navigation to use PW page tree queries
7. Keep the theme's JS plugins and interactivity working

Always also create the standard utility templates:
- `_init.php` — API variable setup, helper includes
- `_func.php` — Reusable helper functions
- `_404.php` — Custom 404 page (styled to match the theme)
- `search.php` — if the theme has search or one is needed

**If building from scratch:**
Based on the site type, create the appropriate template files in `site/templates/`:

**Always create:**
- `_init.php` — API variable setup, helper includes
- `_main.php` — HTML wrapper/shell (doctype, head, nav, footer)
- `_func.php` — Reusable helper functions
- `home.php` — Homepage
- `basic-page.php` — Generic content page
- `_404.php` — Custom 404 page

**Create as needed:**
- `blog-index.php` + `blog-post.php` — if blog features needed
- `product-list.php` + `product.php` — if ecommerce
- `contact.php` — if contact form needed
- `search.php` — if search functionality needed
- `sitemap.xml.php` — XML sitemap template

### Step 6: Define Fields
Generate the field export JSON in `site/install/fields.json` containing all fields needed for the chosen templates. Always include:

- `title` (built-in, but configure per template)
- `body` — CKEditor rich text
- `summary` — plain textarea for excerpts/meta descriptions
- `featured_image` — single image field
- `images` — multi-image gallery field
- `seo_title` — plain text, max 60 chars
- `seo_description` — textarea, max 160 chars

Add template-specific fields based on the site type. If converting a theme, add fields for any dynamic content areas identified during analysis.

### Step 7: Define Templates Export
Generate `site/install/templates.json` with all template definitions and their field assignments.

### Step 8: Generate Page Tree
Create `site/install/pages-tree.json` defining the initial page structure with:
- Page name (URL slug)
- Template assignment
- Parent page
- Status (published/hidden)
- Placeholder content

If converting a theme, the page tree should mirror the pages available in the theme (e.g. if the theme has `about.html`, `contact.html`, `blog.html`, create matching pages).

### Step 9: Create Install Script
Generate two import scripts:

1. **`scripts/install-fields.php`** — standalone version with PW bootstrap logic (for reference/manual use)
2. **`site/templates/run-import.php`** — web-accessible version that runs within PW's template context (the reliable method)

The web-accessible version is the recommended approach because:
- PW's CLI bootstrap doesn't set `$_SERVER['HTTP_HOST']`, breaking environment-aware configs
- The CLI version requires manually injecting `$_SERVER['HTTP_HOST']` before `include 'index.php'`
- The web version has full access to all PW API variables (`$pages`, `$fields`, `$templates`, `$modules`)

The import script must handle:
- **Deferred field resolution** — Page reference fields (like `blog_categories`) store parent/template as paths/names in JSON. These need resolving to IDs after the pages and templates they reference have been created.
- **Deferred template restrictions** — `allowedChildTemplates` and `allowedParentTemplates` need a second pass after all templates exist.
- **Template-specific settings** — `noPrependTemplateFile`, `noAppendTemplateFile`, `contentType`, `sortfield` for utility templates like RSS feeds.

**Important**: Always delete `site/templates/run-import.php` after use — it should never be deployed to production.

### Step 10: Summary
Output a clear summary of everything created:
- Theme used (if any) and which variant
- Templates created (with field assignments)
- Modules to install
- Frontend stack
- Next steps (Docker setup, PW installation, field import)

## Important Notes
- **Always create a project branch** — never scaffold on `main`. Main is the clean starter template.
- Always consult the instruction files before making recommendations
- All field names use lowercase_with_underscores
- All template filenames use lowercase-with-hyphens.php
- Generate complete, production-ready code — not stubs or placeholders
- Include helpful comments in all PHP files
- The PW installer will append config to `site/config.php` — plan for cleaning up duplicates after install
- `composer install` must run inside the Docker container (PHP/Composer aren't guaranteed on the host)
- Module git clones must run on the host machine (Docker can't resolve GitHub DNS)
- Always include `admin.php` in the template files — PW admin breaks without it
- When converting a theme, preserve its visual design faithfully — don't "improve" or modernise the design unless asked
