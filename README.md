# Sound M8

<p align="center">
  <img src="hero.gif" alt="Sound M8" width="500">
</p>

A ProcessWire blog and news site built with Tailwind CSS, HTMX, and Claude Code automation.

## Quick Start

### Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (for local development)
- [Composer](https://getcomposer.org/) (PHP package manager)
- [Node.js](https://nodejs.org/) 18+ (for frontend build tools)
- [Claude Code](https://claude.ai/code) (for AI-assisted development)

### Setup

**macOS / Linux:**
```bash
git clone https://github.com/yourusername/pw-starter.git my-client-site
cd my-client-site
bash scripts/setup.sh
```

**Windows (PowerShell):**
```powershell
git clone https://github.com/yourusername/pw-starter.git my-client-site
cd my-client-site
.\scripts\setup.ps1
```

### Start Development

```bash
# Start Docker containers
cd docker && docker compose up -d --build && cd ..

# Watch frontend changes (separate terminal)
npm run dev

# Visit http://localhost:8080 to run the PW installer
```

### ProcessWire Installation

When the PW installer loads at `http://localhost:8080`:

1. **Database host**: `db`
2. **Database name**: `pw_dev`
3. **Database user**: `pw_user`
4. **Database password**: `pw_password`
5. **Profile**: Blank
6. **Time zone**: Europe/London

After installation, import fields and templates from `site/install/`.

## Claude Code Commands

Open this project in Claude Code and use these slash commands:

| Command | What it does |
|---|---|
| `/new-project` | Full project scaffold — asks about the site, suggests stack, creates templates |
| `/new-template` | Create a new PW template with fields |
| `/add-module` | Install and configure a ProcessWire module |
| `/docker-up` | Start the local Docker development environment |
| `/deploy-checklist` | Pre-launch security, SEO, and performance audit |
| `/export-fields` | Generate field/template/page JSON exports |
| `/new-page` | Add pages to the site tree structure |

## Project Structure

```
├── CLAUDE.md                  # AI instructions (Claude Code reads this)
├── .claude/commands/          # Slash command definitions
├── .claude/instructions/      # Deep reference documentation
├── docker/                    # Docker development environment
├── scripts/                   # Setup and automation scripts
├── site/                      # ProcessWire site profile
│   ├── templates/             # PHP template files
│   ├── assets/src/            # Frontend source files
│   ├── install/               # Field/template export JSON
│   └── config.php             # PW configuration
├── composer.json              # PHP dependencies
├── package.json               # Frontend dependencies
└── tailwind.config.js         # Tailwind CSS configuration
```

## Deployment

Target: Krystal shared hosting. See `.claude/instructions/deployment-krystal.md` for full details, or run `/deploy-checklist` in Claude Code.

## Modules

This project uses free ProcessWire modules only. See `.claude/instructions/module-recommendations.md` for the curated list.

## Licence

Private template repository. Not for public distribution.
