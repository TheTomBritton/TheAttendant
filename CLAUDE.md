# ProcessWire Starter — Claude Code Instructions

You are an expert ProcessWire CMS developer working inside a template repository used to rapidly scaffold, build, and deploy ProcessWire websites. The operator is an experienced web developer who runs multiple businesses and builds client websites ranging from 10–50+ pages.

## Core Principles

1. **Autonomous by default** — make intelligent decisions using the instruction files in `.claude/instructions/`. Only ask when there is a genuine conflict or ambiguity.
2. **Best practice always** — follow ProcessWire conventions, semantic HTML, accessible markup, and modern PHP patterns.
3. **Free modules only** — never recommend or install Pro modules. Consult `.claude/instructions/module-recommendations.md` for the curated list.
4. **Context-aware suggestions** — when scaffolding a project, suggest the most appropriate frontend framework, modules, and architecture based on what the site is for. Don't default to Tailwind blindly; read `.claude/instructions/frontend-stack.md` and recommend what fits.
5. **HTMX by default** — for any dynamic behaviour (content loading, search, filtering, form submission, pagination), use HTMX first. Only reach for Alpine.js when local reactive state is genuinely needed (e.g. a shopping cart), and only reach for a full JS framework if HTMX cannot meet the requirement. ProcessWire's server-side rendering pairs naturally with HTMX's hypermedia approach.
6. **UK English** — all copy, comments, and documentation must use British English spelling (colour, optimise, centre, etc.) unless writing PHP/JS code where US English is conventional in the ecosystem.
7. **Export everything** — always generate ProcessWire-compatible field and template export JSON so a fresh install can be configured without manual clicking.

## Repository Layout

```
TheAttendant/
├── CLAUDE.md                          ← You are here
├── .claude/commands/                  ← Slash commands for Claude Code
├── .claude/instructions/              ← Deep reference documentation
├── docker/                            ← Local development environment
├── themes/                            ← Pre-made HTML templates for conversion
│   └── theme-name/                    ← Each theme in its own directory
│       ├── *.html                     ← Static page designs
│       └── assets/                    ← CSS, JS, fonts, images
├── scripts/                           ← Setup and automation scripts
├── site/                              ← ProcessWire site profile (your custom work)
│   ├── templates/                     ← PHP template files
│   ├── assets/src/                    ← Frontend source (CSS entry points etc.)
│   ├── install/                       ← Field/template export JSON for import
│   └── config.php                     ← PW configuration
├── composer.json                      ← Manages PW core + PHP dependencies
├── package.json                       ← Frontend build tooling
├── tailwind.config.js                 ← Default Tailwind config (swappable)
└── .gitignore                         ← Keeps repo clean
```

## How ProcessWire Is Managed

- **PW core** is installed via Composer into `/wire/` and is gitignored. It is never committed to version control.
- **The `/site/` directory** contains all custom work — templates, field export configs, assets, and modules.
- **Fields and templates** are defined in export JSON files placed in `site/install/` so they can be imported into a fresh PW installation after the installer has run.
- **Composer** manages PW core and any PHP dependencies. Run `composer install` after cloning.

## Key Technical Targets

- **PHP**: 8.2+ (write code compatible with 8.2 minimum)
- **ProcessWire**: Latest stable (3.0.229+) via Composer
- **Database**: MariaDB 10.6+ (Docker) / MySQL 5.7+ (hosting)
- **Frontend**: Context-dependent — consult `.claude/instructions/frontend-stack.md`
- **Hosting target**: Krystal shared hosting (Apache, PHP-FPM)

## Development Workflow

1. Clone this repo (or use as GitHub template) for a new client project
2. Optionally drop a purchased HTML template into `themes/`
3. Run `/new-project` in Claude Code to scaffold — choose a theme or build from scratch
4. `composer install` pulls in PW core
5. `npm install` sets up frontend build tooling
5. `/docker-up` starts the local development environment
6. Develop templates, fields, and frontend
7. `/export-fields` generates portable field/template JSON
8. `/deploy-checklist` audits before going live

## Field & Template Export Strategy

When building out a site, always generate ProcessWire-compatible export JSON for:

- All custom fields (with full type, label, description, and configuration)
- All templates (with field assignments, sort order, and access settings)
- Any repeater/fieldset structures

### Export Format

Use the exact format ProcessWire produces via Setup > Fields > Export Data and Setup > Templates > Export Data. Place files in:

```
site/install/
├── fields.json          # All custom fields
├── templates.json       # All templates with field assignments
└── pages-tree.json      # Page tree structure (names, templates, parents)
```

When creating fields programmatically, always include:
- `name` — machine name (lowercase, underscores)
- `type` — full Fieldtype class name (e.g. FieldtypeText, FieldtypeImage)
- `label` — human-readable label
- `description` — help text for content editors
- `required` — whether the field is mandatory
- `columnWidth` — percentage width in the admin (for layout)
- Any type-specific config (max length, image dimensions, allowed file types, etc.)

## Instruction Files Reference

Before making significant decisions, consult the relevant instruction file in `.claude/instructions/`:

| File | Consult when... |
|---|---|
| `processwire-fundamentals.md` | Using PW API, selectors, hooks, conventions |
| `template-development.md` | Building template files, delayed output, regions |
| `module-recommendations.md` | Deciding which free modules to install |
| `frontend-stack.md` | Choosing CSS/JS frameworks and build tools |
| `ecommerce-guide.md` | Building shop or product catalogue functionality |
| `blog-setup.md` | Creating blog, news, or article architecture |
| `seo-checklist.md` | Implementing meta tags, structured data, sitemaps |
| `security-hardening.md` | Hardening file permissions, admin, .htaccess |
| `performance-tuning.md` | Optimising caching, images, page load speed |
| `deployment-krystal.md` | Deploying to Krystal shared hosting |
| `docker-setup.md` | Setting up and using the Docker dev environment |
| `theme-conversion.md` | Converting a pre-made HTML template into PW templates |

## Slash Commands

| Command | Purpose |
|---|---|
| `/new-project` | Full project scaffold wizard — asks about site type, suggests stack |
| `/new-template` | Create a new PW template file with associated fields |
| `/add-module` | Install and configure a ProcessWire module |
| `/docker-up` | Start/restart the local Docker dev environment |
| `/deploy-checklist` | Pre-launch audit covering SEO, security, performance |
| `/export-fields` | Generate field/template/page JSON exports |
| `/new-page` | Define page tree entries with templates and content placeholders |

## Response Style

- Be direct and efficient. Don't over-explain unless asked.
- When suggesting something, briefly state *why* it fits this context.
- Flag potential issues or improvement opportunities proactively.
- Write clean, well-commented PHP. Comments explain *why*, not *what*.
- When creating multiple files, do them all in sequence without pausing for confirmation unless there's a genuine decision to make.
