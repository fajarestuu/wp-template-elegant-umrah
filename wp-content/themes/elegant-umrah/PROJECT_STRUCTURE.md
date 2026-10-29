# Elegant Umrah - Theme Architecture & Structure

This document outlines the architecture, coding standards, and directory structure of the **Elegant Umrah** WordPress theme. It is designed to help other developers and AI agents understand how the theme operates.

## Overview
- **Theme Type:** Full Site Editing (FSE) Block Theme
- **Primary Configuration:** `theme.json` (Handles global colors, typography, block spacing, and layout constraints)
- **Custom Post Types:** 
  - `umrah_package` (Umrah & Hajj Packages)
- **Custom Taxonomies:**
  - `package_type` (Umrah, Hajj, Plus, VIP, etc.)
- **No Heavy Third-Party Plugins Required:** This theme uses a **custom-built meta field engine** instead of ACF, and registers custom blocks natively.

## Directory Structure

```text
elegant-umrah/
├── archive-umrah_package.php
├── functions.php            # Entry point for loading all inc/ PHP files and theme setup
├── home.php                 # Articles archive (PHP override of home.html; runs PHP logic)
├── index.php                # Fallback for older WP versions (empty, as it's an FSE theme)
├── PROJECT_STRUCTURE.md     # This file
├── single.php               # Single post (PHP override of single.html; runs PHP logic)
├── single-umrah_package.php
├── style.css                # Standard WordPress stylesheet (contains some global utility overrides)
├── theme.json               # GLOBAL CONFIG: Colors, layout sizes, font families
│
├── assets/                  # CSS/JS assets
│   ├── css/
│   │   ├── admin.css        # Styles for the custom meta fields in WP Admin
│   │   └── editor.css       # Custom styles loaded in the Gutenberg editor
│   └── js/
│       ├── admin.js         # Handles meta field interactions
│       ├── main.js          # Frontend interactions
│       └── packages-filter.js # AJAX filtering logic
│
├── blocks/                  # CUSTOM GUTENBERG BLOCKS (Dynamic blocks)
│   ├── hero/
│   │   ├── block.json
│   │   ├── index.asset.php
│   │   ├── index.js
│   │   ├── render.php
│   │   └── style.css
│   ├── package-card/
│   │   ├── block.json
│   │   ├── index.asset.php
│   │   ├── index.js
│   │   ├── render.php
│   │   └── style.css
│   └── testimonial/
│       ├── block.json
│       ├── index.asset.php
│       ├── index.js
│       ├── render.php
│       └── style.css
│
├── inc/                     # CORE PHP LOGIC
│   ├── admin-columns.php    # Custom admin column definitions
│   ├── articles.php         # Articles: AJAX filter, category badge, related posts
│   ├── customizer.php       # Theme customizer options
│   ├── enqueue.php          # Asset enqueuing for frontend, admin, and editor
│   ├── helpers.php          # Helper functions and AJAX endpoints
│   ├── i18n.php             # Internationalization setup
│   ├── post-types.php       # CPT registrations (e.g., umrah_package)
│   ├── setup.php            # Theme setup (theme supports, text domain)
│   ├── taxonomies.php       # Taxonomy registrations
│   └── fields/              # CUSTOM META FIELDS ENGINE
│       ├── field-core.php
│       ├── field-groups.php # DEFINES WHICH FIELDS APPEAR WHERE
│       ├── field-helpers.php
│       ├── field-render.php
│       ├── field-save.php
│       └── field-types.php
│
├── languages/
│   └── README.txt
│
├── parts/                   # FSE TEMPLATE PARTS (HTML)
│   ├── footer.html
│   ├── header.html
│   └── sidebar.html
│
├── patterns/                # NATIVE WORDPRESS BLOCK PATTERNS
│   ├── articles-grid.php    # Reusable 3-up article grid with section header
│   ├── cta-section.php
│   ├── hero.php
│   ├── package-card.php
│   ├── packages-grid.php
│   └── testimonials.php
│
└── templates/               # FSE TEMPLATES (HTML files that dictate page layouts)
    ├── 404.html
    ├── archive-umrah_package.html
    ├── archive.html
    ├── front-page.html
    ├── home.html
    ├── index.html
    ├── page.html
    ├── single-umrah_package.html
    └── single.html
```

## Key Development Rules

### 1. Styling & Layout (FSE First)
- **Do NOT** write hardcoded CSS grids or flexboxes in `style.css` if it conflicts with Gutenberg native blocks (e.g., `wp-block-columns`, `wp-block-group`).
- **Use Native Blocks:** When structuring HTML templates in the `/parts/` or `/templates/` folders, always utilize `wp:group`, `wp:columns`, and `wp:query` for structure. 
- Avoid nesting native blocks (like `wp:navigation`) inside raw `wp:html` blocks. Native blocks require standard block contexts.

### 2. Adding Custom Meta Fields
- You **do not** need a plugin for custom fields. 
- Open `inc/fields/field-groups.php`. Add arrays to the `eu_register_field_group()` configuration.
- The engine supports types: `text`, `textarea`, `number`, `select`, `checkbox`, `image` (attachment ID), `color`, and `date`.
- All fields are automatically saved into standard `postmeta`. Retrieve them using standard `get_post_meta( $post_id, 'eu_field_name', true )`.

### 3. Creating Custom Blocks
- Blocks are Server-Side Rendered (Dynamic). 
- Use the `blocks/` directory. 
- Define properties in `block.json`, define the visual rendering in `render.php`.
- **Important**: Always include an `index.asset.php` file (e.g. returning `['dependencies' => ['wp-blocks']]`) to ensure WordPress core JS loads correctly and prevents `wp is not defined` errors.

### 4. REST API & AJAX
- The theme hooks into standard AJAX (`wp_ajax_` and `wp_ajax_nopriv_`) inside `inc/helpers.php` for dynamic filtering.
- **CRITICAL:** Ensure NO PHP files in the theme contain a **UTF-8 BOM (Byte Order Mark)** or echo unexpected characters outside of standard render paths, as this will break Gutenberg's REST API during Post Publishing/Saving.
