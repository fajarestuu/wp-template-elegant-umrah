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
├── theme.json               # GLOBAL CONFIG: Colors (#0B0B0B, #D4AF37), layout sizes, font families
├── style.css                # Standard WordPress stylesheet (contains some global utility overrides)
├── functions.php            # Entry point for loading all inc/ PHP files and theme setup
├── index.php                # Fallback for older WP versions (empty, as it's an FSE theme)
│
├── assets/                  # CSS/JS assets (Vanilla CSS/JS, no Tailwind/React compilation needed)
│   ├── css/
│   │   ├── admin.css        # Styles for the custom meta fields in WP Admin
│   │   └── editor.css       # Custom styles loaded in the Gutenberg editor
│   └── js/
│       ├── admin.js         # Handles meta field interactions (image uploads, color pickers)
│       ├── main.js          # Frontend interactions (Sticky header, mobile menu)
│       └── packages-filter.js # AJAX filtering logic for the Packages page
│
├── blocks/                  # CUSTOM GUTENBERG BLOCKS (Dynamic blocks)
│   ├── hero/                # Custom Hero block
│   ├── package-card/        # Custom Package Card block (Renders Umrah Packages data)
│   └── testimonial/         # Custom Testimonial block
│   # Note: Each block contains `block.json`, `render.php` (server-side rendering), and `index.asset.php` to manage WP dependencies.
│
├── inc/                     # CORE PHP LOGIC
│   ├── setup.php            # Theme setup (theme supports, text domain)
│   ├── enqueue.php          # Asset enqueuing for frontend, admin, and editor
│   ├── post-types.php       # CPT registrations (e.g., umrah_package)
│   ├── taxonomies.php       # Taxonomy registrations
│   ├── helpers.php          # Helper functions and AJAX endpoints (e.g., package filtering)
│   │
│   └── fields/              # CUSTOM META FIELDS ENGINE (Native replacement for ACF)
│       ├── field-core.php   # Meta boxes registration logic
│       ├── field-groups.php # DEFINES WHICH FIELDS APPEAR WHERE (Edit this to add new fields!)
│       ├── field-render.php # HTML rendering for admin form fields
│       ├── field-save.php   # Logic for saving $_POST data securely
│       └── field-types.php  # Schema definitions (text, select, image, checkboxes)
│
├── parts/                   # FSE TEMPLATE PARTS (HTML)
│   ├── header.html          # Global header part
│   ├── footer.html          # Global footer part (Built using native wp:columns)
│   └── sidebar.html         # Sidebar component
│
├── patterns/                # NATIVE WORDPRESS BLOCK PATTERNS (PHP files with header comments)
│   ├── hero.php
│   ├── testimonials.php
│   ├── package-card.php
│   ├── packages-grid.php
│   └── cta-section.php
│
└── templates/               # FSE TEMPLATES (HTML files that dictate page layouts)
    ├── front-page.html      # Homepage layout
    ├── index.html           # Main blog/fallback layout
    ├── archive-umrah_package.html # Packages archive listing
    ├── single-umrah_package.html  # Single package details page
    └── 404.html             # Error page
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
