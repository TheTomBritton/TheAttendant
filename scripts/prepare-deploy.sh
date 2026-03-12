#!/usr/bin/env bash
#
# prepare-deploy.sh — Build and package Maid of Threads for Krystal hosting
#
# Usage: ./scripts/prepare-deploy.sh
#
# Creates a deploy-ready directory at ./deploy/ containing only the files
# needed for SFTP upload. Upload the contents of ./deploy/ to public_html/.

set -euo pipefail

REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
DEPLOY_DIR="$REPO_ROOT/deploy"
PROJECT_DIR="$REPO_ROOT/sites/maid-of-threads"

echo "=== Maid of Threads — Deployment Preparation ==="
echo ""

# ── Step 1: Build production assets ──────────────────────────────
echo "[1/4] Building production assets..."
cd "$PROJECT_DIR"
npm run build
echo "     Done — site/assets/dist/ updated."
echo ""

# ── Step 2: Check PW core exists ─────────────────────────────────
echo "[2/4] Checking ProcessWire core..."
if [ ! -d "$REPO_ROOT/wire/core" ]; then
    echo "     ERROR: wire/ directory not found."
    echo "     Run 'composer install --no-dev' first (in Docker if no local PHP):"
    echo "       docker compose exec php composer install --no-dev"
    exit 1
fi
echo "     Done — wire/ found."
echo ""

# ── Step 3: Check Stripe SDK exists ──────────────────────────────
echo "[3/4] Checking Stripe SDK..."
if [ ! -d "$REPO_ROOT/vendor/stripe" ]; then
    echo "     ERROR: vendor/stripe/ not found."
    echo "     Run 'composer install --no-dev' first."
    exit 1
fi
echo "     Done — vendor/stripe/ found."
echo ""

# ── Step 4: Assemble deploy directory ────────────────────────────
echo "[4/4] Assembling deploy package..."

# Clean previous deploy
rm -rf "$DEPLOY_DIR"
mkdir -p "$DEPLOY_DIR/site/templates"
mkdir -p "$DEPLOY_DIR/site/modules"
mkdir -p "$DEPLOY_DIR/site/assets/dist"
mkdir -p "$DEPLOY_DIR/site/assets/files"
mkdir -p "$DEPLOY_DIR/site/assets/cache"
mkdir -p "$DEPLOY_DIR/site/assets/logs"
mkdir -p "$DEPLOY_DIR/site/assets/sessions"

# PW core
cp -R "$REPO_ROOT/wire" "$DEPLOY_DIR/wire"

# Vendor (Composer autoload + Stripe SDK)
cp -R "$REPO_ROOT/vendor" "$DEPLOY_DIR/vendor"

# Root files
cp "$REPO_ROOT/index.php" "$DEPLOY_DIR/"
cp "$REPO_ROOT/.htaccess" "$DEPLOY_DIR/"

# Site config (from the project overlay)
cp "$PROJECT_DIR/config.php" "$DEPLOY_DIR/site/config.php"

# Templates
cp -R "$PROJECT_DIR/templates/"* "$DEPLOY_DIR/site/templates/"

# Built assets
cp -R "$REPO_ROOT/site/assets/dist/"* "$DEPLOY_DIR/site/assets/dist/"

# Modules (from Docker/PW install)
if [ -d "$REPO_ROOT/site/modules" ]; then
    cp -R "$REPO_ROOT/site/modules/"* "$DEPLOY_DIR/site/modules/" 2>/dev/null || true
fi

# init.php, ready.php if they exist at the site level
for f in init.php ready.php; do
    if [ -f "$REPO_ROOT/site/$f" ]; then
        cp "$REPO_ROOT/site/$f" "$DEPLOY_DIR/site/$f"
    fi
done

echo "     Done — deploy/ directory assembled."
echo ""

# ── Summary ──────────────────────────────────────────────────────
DEPLOY_SIZE=$(du -sh "$DEPLOY_DIR" | cut -f1)
echo "=== Deploy package ready: $DEPLOY_SIZE ==="
echo ""
echo "Next steps:"
echo "  1. Update site/config.php with Krystal DB credentials"
echo "  2. Export database from Docker:"
echo "       docker compose exec db mysqldump -u root -p mot_dev > database-export.sql"
echo "  3. Upload deploy/ contents to Krystal public_html/ via SFTP"
echo "  4. Import database via cPanel phpMyAdmin"
echo "  5. Rename processwire/ admin directory to mot-studio/"
echo "  6. Visit your site and log into /mot-studio/ to verify"
echo ""
echo "IMPORTANT: Do NOT upload site/assets/files/ or site/assets/cache/"
echo "           from your local machine — let PW create these fresh."
