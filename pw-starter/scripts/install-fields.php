<?php namespace ProcessWire;

/**
 * Field, Template & Page Import Script
 *
 * Run this after a fresh ProcessWire installation to import all project
 * fields, templates, and page structure from JSON exports.
 *
 * Usage:
 *   Option A: Place in site/templates/, visit via browser, then remove.
 *   Option B: Run via PW CLI: php site/templates/install-fields.php
 *   Option C: Copy to site root and run: php install-fields.php
 *
 * This script reads from:
 *   site/install/fields.json
 *   site/install/templates.json
 *   site/install/pages-tree.json
 */

// Bootstrap ProcessWire if not already loaded
if (!defined('PROCESSWIRE')) {
    // Adjust path if running from different locations
    $paths = [
        __DIR__ . '/../../index.php',      // From site/templates/
        __DIR__ . '/../index.php',          // From site/
        __DIR__ . '/index.php',             // From root
        './index.php',                       // Current directory
    ];

    $bootstrapped = false;
    foreach ($paths as $path) {
        if (file_exists($path)) {
            include $path;
            $bootstrapped = true;
            break;
        }
    }

    if (!$bootstrapped) {
        die("Error: Could not find ProcessWire index.php. Run this from the PW root directory.\n");
    }
}

$installDir = wire('config')->paths->site . 'install/';
$results = ['fields' => 0, 'templates' => 0, 'pages' => 0, 'errors' => []];

echo "=== ProcessWire Field & Template Import ===\n\n";

// ──────────────────────────────────────────────
// STEP 1: Import Fields
// ──────────────────────────────────────────────
$fieldsFile = $installDir . 'fields.json';
if (file_exists($fieldsFile)) {
    $fieldsData = json_decode(file_get_contents($fieldsFile), true);

    if ($fieldsData) {
        echo "Importing fields...\n";

        foreach ($fieldsData as $fieldData) {
            $name = $fieldData['name'] ?? '';
            if (!$name) continue;

            // Skip if field already exists
            $existing = wire('fields')->get($name);
            if ($existing) {
                echo "  [skip] Field '{$name}' already exists\n";
                continue;
            }

            try {
                $field = new Field();
                $field->type = wire('modules')->get($fieldData['type']);
                $field->name = $name;
                $field->label = $fieldData['label'] ?? $name;
                $field->description = $fieldData['description'] ?? '';
                $field->tags = $fieldData['tags'] ?? '';
                $field->required = $fieldData['required'] ?? false;
                $field->columnWidth = $fieldData['columnWidth'] ?? 100;

                // Apply type-specific settings
                if (isset($fieldData['settings'])) {
                    foreach ($fieldData['settings'] as $key => $value) {
                        $field->set($key, $value);
                    }
                }

                // Input field class (e.g. InputfieldCKEditor for rich text)
                if (isset($fieldData['inputfieldClass'])) {
                    $field->inputfieldClass = $fieldData['inputfieldClass'];
                }

                $field->save();
                $results['fields']++;
                echo "  [ok] Created field: {$name} ({$fieldData['type']})\n";

            } catch (\Exception $e) {
                $results['errors'][] = "Field '{$name}': " . $e->getMessage();
                echo "  [error] Field '{$name}': " . $e->getMessage() . "\n";
            }
        }
    }
} else {
    echo "No fields.json found — skipping field import.\n";
}

echo "\n";

// ──────────────────────────────────────────────
// STEP 2: Import Templates
// ──────────────────────────────────────────────
$templatesFile = $installDir . 'templates.json';
if (file_exists($templatesFile)) {
    $templatesData = json_decode(file_get_contents($templatesFile), true);

    if ($templatesData) {
        echo "Importing templates...\n";

        foreach ($templatesData as $tplData) {
            $name = $tplData['name'] ?? '';
            if (!$name) continue;

            // Skip if template already exists
            $existing = wire('templates')->get($name);
            if ($existing) {
                echo "  [skip] Template '{$name}' already exists\n";

                // Still update field assignments
                if (isset($tplData['fields'])) {
                    $fg = $existing->fieldgroup;
                    foreach ($tplData['fields'] as $fieldName) {
                        $field = wire('fields')->get($fieldName);
                        if ($field && !$fg->hasField($field)) {
                            $fg->add($field);
                            echo "    [ok] Added field '{$fieldName}' to existing template\n";
                        }
                    }
                    $fg->save();
                }
                continue;
            }

            try {
                // Create fieldgroup
                $fg = new Fieldgroup();
                $fg->name = $name;

                // Add fields
                if (isset($tplData['fields'])) {
                    foreach ($tplData['fields'] as $fieldName) {
                        $field = wire('fields')->get($fieldName);
                        if ($field) {
                            $fg->add($field);
                        } else {
                            echo "    [warn] Field '{$fieldName}' not found for template '{$name}'\n";
                        }
                    }
                }

                $fg->save();

                // Create template
                $template = new Template();
                $template->name = $name;
                $template->fieldgroup = $fg;
                $template->label = $tplData['label'] ?? '';
                $template->tags = $tplData['tags'] ?? '';

                if (isset($tplData['noChildren'])) $template->noChildren = (int)$tplData['noChildren'];
                if (isset($tplData['noParents'])) $template->noParents = (int)$tplData['noParents'];
                if (isset($tplData['urlSegments'])) $template->urlSegments = (int)$tplData['urlSegments'];

                $template->save();

                // Set field widths
                if (isset($tplData['fieldWidths'])) {
                    foreach ($tplData['fieldWidths'] as $fieldName => $width) {
                        $field = wire('fields')->get($fieldName);
                        if ($field) {
                            $fg->setFieldContextArray($field->id, ['columnWidth' => $width]);
                        }
                    }
                    $fg->saveContext();
                }

                $results['templates']++;
                echo "  [ok] Created template: {$name}\n";

            } catch (\Exception $e) {
                $results['errors'][] = "Template '{$name}': " . $e->getMessage();
                echo "  [error] Template '{$name}': " . $e->getMessage() . "\n";
            }
        }
    }
} else {
    echo "No templates.json found — skipping template import.\n";
}

echo "\n";

// ──────────────────────────────────────────────
// STEP 3: Import Page Tree
// ──────────────────────────────────────────────
$pagesFile = $installDir . 'pages-tree.json';
if (file_exists($pagesFile)) {
    $pagesData = json_decode(file_get_contents($pagesFile), true);

    if ($pagesData) {
        echo "Creating page tree...\n";

        foreach ($pagesData as $pageData) {
            $name = $pageData['name'] ?? '';
            $parentPath = $pageData['parent'] ?? '/';
            $templateName = $pageData['template'] ?? 'basic-page';

            if (!$name) continue;

            // Find parent
            $parent = wire('pages')->get($parentPath);
            if (!$parent->id) {
                echo "  [warn] Parent '{$parentPath}' not found for page '{$name}'\n";
                continue;
            }

            // Check if page already exists
            $existing = $parent->child("name={$name}, include=all");
            if ($existing->id) {
                echo "  [skip] Page '{$name}' already exists under {$parentPath}\n";
                continue;
            }

            // Get template
            $template = wire('templates')->get($templateName);
            if (!$template) {
                echo "  [warn] Template '{$templateName}' not found for page '{$name}'\n";
                continue;
            }

            try {
                $p = new Page();
                $p->template = $template;
                $p->parent = $parent;
                $p->name = $name;
                $p->title = $pageData['title'] ?? ucfirst(str_replace('-', ' ', $name));

                if (isset($pageData['sort'])) $p->sort = $pageData['sort'];

                $status = $pageData['status'] ?? 'published';
                if ($status === 'hidden') $p->addStatus(Page::statusHidden);
                if ($status === 'unpublished') $p->addStatus(Page::statusUnpublished);

                $p->save();

                // Set content fields
                if (isset($pageData['content'])) {
                    foreach ($pageData['content'] as $fieldName => $value) {
                        if ($p->template->fieldgroup->hasField($fieldName)) {
                            $p->set($fieldName, $value);
                        }
                    }
                    $p->save();
                }

                $results['pages']++;
                echo "  [ok] Created page: {$parentPath}{$name}/ ({$templateName})\n";

            } catch (\Exception $e) {
                $results['errors'][] = "Page '{$name}': " . $e->getMessage();
                echo "  [error] Page '{$name}': " . $e->getMessage() . "\n";
            }
        }
    }
} else {
    echo "No pages-tree.json found — skipping page tree import.\n";
}

// ──────────────────────────────────────────────
// Summary
// ──────────────────────────────────────────────
echo "\n=== Import Complete ===\n";
echo "Fields created:    {$results['fields']}\n";
echo "Templates created: {$results['templates']}\n";
echo "Pages created:     {$results['pages']}\n";

if (count($results['errors'])) {
    echo "\nErrors (" . count($results['errors']) . "):\n";
    foreach ($results['errors'] as $error) {
        echo "  - {$error}\n";
    }
}

echo "\nDone.\n";
