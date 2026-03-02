# Frontend Stack Selection Guide

## Decision Framework

Choose the frontend stack based on the project's needs, not habit. Use this guide to recommend the right tools for each project.

## CSS Frameworks

### Tailwind CSS (Default Recommendation)
**Best for**: Most projects. Brochure sites, blogs, dashboards, custom designs.
**Why**: Utility-first, highly customisable, small production builds with PurgeCSS, excellent documentation.
**Avoid when**: The project is very small (under 5 pages) with minimal styling, or when the client needs to edit CSS directly.

**Setup included in this repo by default:**
```bash
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init
```

Entry point: `site/assets/src/app.css`
```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

Build command: `npx tailwindcss -i ./site/assets/src/app.css -o ./site/assets/dist/app.css --watch`

### Bootstrap 5
**Best for**: Projects where the client or another developer may maintain the site. Admin panels. Sites needing robust component library quickly.
**Why**: Well-known, comprehensive components (modals, dropdowns, carousels), good documentation.
**Avoid when**: You need a highly custom design (Bootstrap sites tend to look similar), or file size is a concern.

**Setup:**
```bash
npm install bootstrap @popperjs/core
```

### Pico CSS
**Best for**: Very small sites (under 10 pages), documentation sites, projects where you want semantic HTML to look good without classes.
**Why**: Classless or minimal-class approach. Drop it in and HTML looks good immediately. ~10KB.
**Avoid when**: Complex layouts, heavy customisation needed.

**Setup:**
```bash
npm install @picocss/pico
```

### No Framework (Custom CSS)
**Best for**: When the design is very specific, performance is critical, or you want full control.
**Why**: Smallest possible CSS, no bloat, complete design freedom.
**Avoid when**: Speed of development is a priority.

**Approach**: Use CSS custom properties, modern CSS features (grid, container queries, nesting).

## JavaScript

### Vanilla JS (Default)
**Best for**: Most ProcessWire sites. PW handles routing and rendering server-side — JS is for enhancement.
**Why**: No build step overhead, no framework learning curve, fastest performance.
**Use for**: Mobile menus, accordions, form validation, scroll effects, lazy loading.

### Alpine.js
**Best for**: When you need reactive UI components without a full framework. Interactive elements within server-rendered pages.
**Why**: ~17KB, works directly in HTML with directives, no build step needed.
**Use for**: Dropdowns, tabs, modals, toggles, search filters, shopping carts.

```html
<div x-data="{ open: false }">
    <button @click="open = !open">Menu</button>
    <nav x-show="open" x-transition>...</nav>
</div>
```

**Setup:**
```html
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

### HTMX
**Best for**: When you need dynamic page updates without full page reloads. Works brilliantly with PW's server-side rendering.
**Why**: ~14KB, progressive enhancement, no build step, works with any backend.
**Use for**: Infinite scroll, live search, form submission without reload, tab content loading.

```html
<button hx-get="/api/load-more/" hx-target="#results" hx-swap="beforeend">
    Load More
</button>
```

### Swiper
**Best for**: When you need a carousel/slider. The gold standard.
**Why**: Touch-friendly, accessible, highly configurable, well-maintained.
**Setup:**
```bash
npm install swiper
```

### GSAP (GreenSock)
**Best for**: Projects needing polished animations.
**Why**: Industry standard for web animation, excellent performance, timeline sequencing.
**Setup:**
```bash
npm install gsap
```

## Build Tools

### Vite (Recommended for Tailwind projects)
**Best for**: Most projects. Fast dev server, efficient builds, HMR.
**Setup:**
```bash
npm install -D vite
```

**vite.config.js:**
```js
import { defineConfig } from 'vite';

export default defineConfig({
    build: {
        outDir: 'site/assets/dist',
        rollupOptions: {
            input: 'site/assets/src/app.css',
        },
    },
});
```

### Tailwind CLI (Simple alternative)
**Best for**: When Tailwind is the only build tool needed and you want to avoid Vite complexity.
**Setup**: Already configured via `package.json` scripts in this repo.

## Recommendations by Project Type

### Business Brochure (5–20 pages)
- **CSS**: Tailwind CSS
- **JS**: Vanilla JS + Alpine.js if interactive elements needed
- **Build**: Tailwind CLI
- **Why**: Fast to build, easy to customise, small output

### Blog / News Site
- **CSS**: Tailwind CSS
- **JS**: Vanilla JS + HTMX for infinite scroll / live search
- **Build**: Vite
- **Why**: HTMX makes dynamic listing pages feel modern without SPA complexity

### Portfolio / Creative Site
- **CSS**: Tailwind CSS or Custom CSS (depends on design complexity)
- **JS**: GSAP for animations, Swiper for galleries
- **Build**: Vite
- **Why**: Creative sites need animation control and custom visual treatment

### Ecommerce
- **CSS**: Tailwind CSS
- **JS**: Alpine.js for cart/product interactions, HTMX for dynamic filtering
- **Build**: Vite
- **Why**: Alpine handles reactive cart state, HTMX manages product filtering without full page reloads

### Directory / Listing Site
- **CSS**: Tailwind CSS
- **JS**: Alpine.js + HTMX
- **Build**: Vite
- **Why**: Filter-heavy sites benefit from HTMX's server-driven updates

### Simple/Small Site (under 5 pages)
- **CSS**: Pico CSS or Custom CSS
- **JS**: Vanilla JS (minimal)
- **Build**: None needed
- **Why**: Minimal overhead for minimal sites

## Font Loading

Always self-host fonts for performance and privacy (no Google Fonts CDN):

```css
@font-face {
    font-family: 'Inter';
    src: url('/site/templates/assets/fonts/inter-v13-latin-regular.woff2') format('woff2');
    font-weight: 400;
    font-style: normal;
    font-display: swap;
}
```

Use [google-webfonts-helper](https://gwfh.mranftl.com/fonts) to download self-hosted font files.

## CDN vs Local Assets

**Default: local assets.** Self-host everything for:
- Privacy (no third-party tracking)
- Reliability (no external dependency)
- Performance (reduced DNS lookups)

Only use CDN for:
- Alpine.js / HTMX (small enough that CDN is acceptable for prototyping)
- Font Awesome icons (if using — consider Heroicons or Lucide as lighter alternatives)

For production, always bundle everything locally.
