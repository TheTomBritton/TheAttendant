#
# pw-starter setup script (Windows PowerShell)
# Bootstraps a new ProcessWire project from this template.
#

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  ProcessWire Starter - Project Setup"   -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Check prerequisites
if (-not (Get-Command composer -ErrorAction SilentlyContinue)) {
    Write-Host "Error: Composer is not installed. Install from https://getcomposer.org" -ForegroundColor Red
    exit 1
}

if (-not (Get-Command npm -ErrorAction SilentlyContinue)) {
    Write-Host "Error: npm is not installed. Install Node.js from https://nodejs.org" -ForegroundColor Red
    exit 1
}

if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
    Write-Host "Warning: Docker is not installed. You'll need it for local development." -ForegroundColor Yellow
}

# Install PHP dependencies (ProcessWire core)
Write-Host "Installing PHP dependencies via Composer..." -ForegroundColor Green
composer install

# Install frontend dependencies
Write-Host "Installing frontend dependencies via npm..." -ForegroundColor Green
npm install

# Set up Docker environment
if (-not (Test-Path "docker\.env")) {
    Write-Host "Creating Docker environment file..." -ForegroundColor Green
    Copy-Item "docker\.env.example" "docker\.env"
    Write-Host "Please edit docker\.env with your project-specific values." -ForegroundColor Yellow
}

# Create required directories
Write-Host "Creating required directories..." -ForegroundColor Green
$dirs = @(
    "site\assets\files",
    "site\assets\cache",
    "site\assets\logs",
    "site\assets\sessions",
    "site\assets\dist",
    "site\templates\partials"
)
foreach ($dir in $dirs) {
    if (-not (Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
    }
}

# Build frontend assets
Write-Host "Building frontend assets..." -ForegroundColor Green
npm run build

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Setup complete!"                       -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next steps:" -ForegroundColor White
Write-Host "  1. Edit docker\.env with your project name and credentials"
Write-Host "  2. cd docker; docker compose up -d --build"
Write-Host "  3. Visit http://localhost:8080 to run the PW installer"
Write-Host "  4. Import fields from site\install\fields.json"
Write-Host "  5. Import templates from site\install\templates.json"
Write-Host ""
