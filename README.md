# Attached: WP Media Audit

## A Claude Code vs GitHub Copilot Demo

A comparison demo building a real WordPress plugin using two AI coding tools.
The plugin extends the native Media Library to surface whether each media item
is used anywhere on the site, and where.

---

## Overview

**Plugin name:** Attached: WP Media Audit
**Goal:** Extend the existing WordPress Media Library screen with a usage filter — no new admin pages. Each media item should show whether it is used or unused, and where it appears.  
**Demo format:** Same prompt, same environment, side-by-side comparison of Claude Code and GitHub Copilot.

---

## Environment

- **Local tool:** WordPress Studio  
- **Sites:** Two identical sites — one per tool  
- **Setup order:** Build and seed one site, duplicate it, then start the demo  
- **Plugin folder:** Set a custom location in Studio and reference the same path in both tools  
- **WP-CLI:** Available in Studio, prefixed with `studio` (e.g. `studio wp ...`)  
- **Local URLs:** Each site gets its own `localhost` with a unique port

---

## Test Site Setup

### 1. Install WordPress Studio
Download from [developer.wordpress.com/studio](https://developer.wordpress.com/studio/) if not already installed.

### 2. Create the primary site
Name it something neutral like `attached-demo`.

### 3. Set the plugin folder path
In Studio settings, set a known folder location for the site. Note the full path — you will paste this into both tools at the start of the demo.

### 4. Seed the media library
Add content that covers the edge cases without over-explaining them in the prompt. Aim for 8–10 media items total so the table is readable on screen.

- Embedded in a post body (classic editor block)
- Set as featured image only — not in post content
- Used in a Gutenberg Image block (stores attachment ID)
- Part of a Gutenberg Gallery block
- Part of the same Gallery block
- Linked in post content by URL
- Not used anywhere
- Not used anywhere (non-image file)

This gives you **ground truth** before either tool writes a line of code. You know exactly what correct output looks like.

### 5. Duplicate the site
Once seeded, duplicate the site in Studio to create a second identical copy. Name it something like `attached-demo-copilot`. Do not touch either site's content again after this point.

### 6. Set up the plugin scaffold
In the plugin folder for each site, create the following structure before the demo begins:

```
attached/
├── attached.php
├── uninstall.php
├── CLAUDE.md              ← Claude Code reads this automatically
└── includes/
    └── .gitkeep
```

Copy the scaffold files below into each site's plugin folder. They should be identical on both sites.

---

## Plugin Scaffold

### `attached.php`

```php
<?php
/**
 * Plugin Name: Attached
 * Plugin URI:  https://github.com/your-handle/attached
 * Description: Extends the WordPress Media Library to show whether each item is used anywhere on the site.
 * Version:     0.1.0
 * Author:      Your Name
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: attached
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ATTACHED_VERSION', '0.1.0' );
define( 'ATTACHED_PATH', plugin_dir_path( __FILE__ ) );
define( 'ATTACHED_URL', plugin_dir_url( __FILE__ ) );

register_activation_hook( __FILE__, 'attached_activate' );
function attached_activate() {
	// Activation tasks will go here.
}

// Bootstrap — the tool will build this out.
```

### `uninstall.php`

```php
<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Cleanup tasks will go here (delete options, transients, etc.)
```

---

## Context File (`CLAUDE.md` / Copilot Instructions)

Place this file at the plugin root. Claude Code reads `CLAUDE.md` automatically. For Copilot, reference it manually in your first message or place it at `.github/copilot-instructions.md` in the project root.

```markdown
# Attached — WordPress Plugin

## Environment
- WordPress Studio (local development)
- WordPress coding standards (WPCS) apply
- PHP 8.1+

## Plugin purpose
Extends the existing Media Library screen to surface whether each media item
is used anywhere on the site, and where. Adds filter controls to the existing
Media Library — does not create new admin pages.

## Constraints
- No external dependencies or Composer packages
- No build step — plain PHP, CSS, and JS only
- Use the `attached_` prefix for all functions, hooks, options, and transients
- Escape all output, sanitize all input, use nonces where appropriate
- Query performance matters — the Media Library can have thousands of items
```

---

## The Prompt

Use the same prompt verbatim for both tools. Fill in the actual plugin folder path before running.

```
I'm working in a local WordPress development site running in WordPress Studio.
The plugin folder is at [YOUR PATH HERE]. I want to build a WordPress plugin
called "Attached" that audits media library usage.

Rather than adding a new admin page, I want to extend the existing Media
Library screen. It should add a filter that lets me view all media, or narrow
down to only used or only unused items. For used items, I should be able to
see where they're used with links to edit those posts/pages.

Before writing any code, I'd like you to plan the approach. Walk me through
how you'd structure the plugin, how you'd determine whether a media item is
"used", and what edge cases or limitations we should be aware of. Once I'm
happy with the plan, we'll build it.
```

---

## What to Watch For

These are the moments that will differentiate the two tools.

### In the planning phase
- Does it mention **featured images** (`_thumbnail_id` postmeta) unprompted?
- Does it mention **Gutenberg block attributes** (attachment IDs in block comments) vs. just URL matching?
- Does it flag **image size variants** (`photo-300x200.jpg` ≠ `photo.jpg`)?
- Does it mention **performance** considerations for large libraries?
- Does it acknowledge its own **limitations** honestly?

### In the build phase
- Does it use proper WordPress APIs (`WP_Query`, `get_posts`) or raw SQL shortcuts?
- Does it hook into the right places (`restrict_manage_posts`, `parse_query`) to extend the existing Media Library rather than reinvent it?
- Does the plugin **actually work** on first activation, or does it need debugging?

### In follow-up rounds
Plan at least one follow-up after the initial build. A good candidate:

> "I noticed it's not catching images that are only set as featured images — can you fix that?"

How each tool responds to being told it missed something is often more revealing than the initial output.

---

## Scoring

| Criterion | Claude Code | GitHub Copilot |
|-----------|-------------|----------------|
| Featured image detection (unprompted) | | |
| Gutenberg block ID detection | | |
| Hooks into existing Media Library screen | | |
| Works on first activation | | |
| Code quality / WordPress standards | | |
| Honest about limitations | | |
| Handles follow-up correction well | | |