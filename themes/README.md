# Themes Directory

Pre-made HTML templates to be converted into ProcessWire sites during the `/new-project` scaffold wizard.

## How It Works

1. Drop a purchased/downloaded HTML template into this directory as a subdirectory
2. Run `/new-project` and select the theme when prompted
3. Claude analyses the HTML files and converts them into PW templates, extracting:
   - Common layout (header, nav, footer) → `_main.php`
   - Individual page types → PW template files
   - CSS/JS/fonts/images → `site/assets/`
   - Sections and components → reusable partials

## Expected Theme Structure

Each theme should be a directory containing static HTML files and an `assets/` folder:

```
themes/
└── theme-name/
    ├── index.html              ← Homepage design
    ├── about.html              ← Inner page examples
    ├── blog.html               ← Blog listing (if applicable)
    ├── blog-single.html        ← Blog post (if applicable)
    ├── contact.html            ← Contact page (if applicable)
    ├── portfolio.html          ← Portfolio/gallery (if applicable)
    └── assets/
        ├── css/                ← Stylesheets
        ├── js/                 ← Scripts
        ├── images/             ← Demo/placeholder images
        ├── fonts/              ← Web fonts
        └── sass/               ← Source SASS/SCSS (if available)
```

## Conversion Process

During scaffolding, Claude will:

1. **Analyse** all HTML files to identify the common shell (header, nav, footer) and unique page sections
2. **Extract** the common shell into `_main.php` using PW's region/delayed output pattern
3. **Convert** each unique page type into a PW template file with appropriate fields
4. **Copy** static assets (CSS, JS, fonts) into `site/assets/dist/` or `site/assets/src/` as appropriate
5. **Replace** static content with PW API calls (`$page->title`, `$page->body`, etc.)
6. **Generate** field and template export JSON matching the content structure
7. **Adapt** navigation to use PW's page tree instead of hardcoded links

## Notes

- Theme images are demo placeholders — they won't be committed to the project branch
- CSS frameworks bundled with the theme (Bootstrap, etc.) are kept as-is rather than replaced
- The SASS source (if present) can be integrated into the project's build pipeline
- Font Awesome / icon fonts are kept; consider suggesting a modern alternative during scaffold
