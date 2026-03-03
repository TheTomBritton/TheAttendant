# /add-module — Install and Configure a ProcessWire Module

## Purpose
Install a free ProcessWire module, configure it with sensible defaults, and document the setup.

## Workflow

### Step 1: Identify the Module
If the operator specifies a module, look it up. If they describe a need (e.g. "I need a form builder"), consult `.claude/instructions/module-recommendations.md` and recommend the best free option with reasoning.

### Step 2: Installation Method

**Via git clone from the HOST machine (preferred):**
```bash
git clone --depth 1 <repo-url> site/modules/<ModuleName>
```
**IMPORTANT**: Always clone from the host machine, never from inside the Docker container. Docker containers typically cannot resolve GitHub DNS. The `site/modules/` directory is volume-mounted and immediately visible inside the container.

**Via Composer (if available — less common for PW modules):**
```bash
docker exec <project>-web composer require <vendor>/<module-name>
```

### Step 2b: Activate the Module
After downloading, activate via PW CLI with HTTP_HOST set:
```bash
docker exec <project>-web bash -c '
export HTTP_HOST=localhost:8080
php -d variables_order=EGPCS -r "
\$_SERVER[\"HTTP_HOST\"] = \"localhost:8080\";
include \"/var/www/html/index.php\";
wire(\"modules\")->refresh();
wire(\"modules\")->install(\"<ModuleName>\");
echo \"Installed <ModuleName>\n\";
"
'
```

Or for batch installs, create a temporary `site/templates/run-modules.php` script that loops through module names calling `$modules->refresh()` then `$modules->install()`. Delete after use.

### Step 3: Configuration
After installation, provide:
1. Any required module configuration settings
2. Recommended configuration for the project context
3. Template code snippets showing how to use the module
4. Any hooks or API usage patterns

### Step 4: Update Documentation
- Add the module to the project's module list in `README.md`
- Note any template dependencies or required fields
- If the module requires fields, add them to the exports

### Step 5: Verify Compatibility
Check:
- PHP 8.2+ compatibility
- ProcessWire 3.0.229+ compatibility
- No conflicts with already-installed modules
- No Pro module dependencies

## Common Module Tasks

### "I need a contact form"
→ Recommend FormBuilder alternative: **FrontendForms** module
→ Create the form template code
→ Set up email notification configuration

### "I need better image handling"
→ Recommend **Croppable Image 3** for focal-point cropping
→ Configure image field settings for responsive output

### "I need SEO tools"
→ Recommend **SeoMaestro** for meta tags and social sharing
→ Set up fields and template integration

### "I need a sitemap"
→ Recommend **MarkupSitemap** module
→ Configure with appropriate change frequencies and priorities

Always reference `.claude/instructions/module-recommendations.md` for the full curated list.
