#!/bin/bash
#
# pw-starter setup script (macOS / Linux)
# Bootstraps a new ProcessWire project from this template.
#
set -e

echo "========================================"
echo "  ProcessWire Starter — Project Setup"
echo "========================================"
echo ""

# Check prerequisites
command -v composer >/dev/null 2>&1 || { echo "Error: Composer is not installed. Install from https://getcomposer.org"; exit 1; }
command -v npm >/dev/null 2>&1 || { echo "Error: npm is not installed. Install Node.js from https://nodejs.org"; exit 1; }
command -v docker >/dev/null 2>&1 || { echo "Warning: Docker is not installed. You'll need it for local development."; }

# Install PHP dependencies (ProcessWire core)
echo "Installing PHP dependencies via Composer..."
composer install

# Install frontend dependencies
echo "Installing frontend dependencies via npm..."
npm install

# Set up Docker environment
if [ ! -f docker/.env ]; then
    echo "Creating Docker environment file..."
    cp docker/.env.example docker/.env
    echo "Please edit docker/.env with your project-specific values."
fi

# Create required directories
echo "Creating required directories..."
mkdir -p site/assets/files
mkdir -p site/assets/cache
mkdir -p site/assets/logs
mkdir -p site/assets/sessions
mkdir -p site/assets/dist
mkdir -p site/templates/partials

# Set permissions
echo "Setting file permissions..."
chmod -R 755 site/assets/

# Build frontend assets
echo "Building frontend assets..."
npm run build

echo ""
echo "========================================"
echo "  Setup complete!"
echo "========================================"
echo ""
echo "Next steps:"
echo "  1. Edit docker/.env with your project name and credentials"
echo "  2. cd docker && docker compose up -d --build"
echo "  3. Visit http://localhost:8080 to run the PW installer"
echo "  4. Import fields from site/install/fields.json"
echo "  5. Import templates from site/install/templates.json"
echo ""
