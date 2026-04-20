# WPHelpers

Helper functions for custom themes or plugins. This package provides a collection of static helper classes and global utility functions to streamline WordPress development.

## Table of Contents

- [Installation](#installation)
- [Classes and Features](#classes-and-features)
  - [AdminHelper](#adminhelper)
  - [BaseHelper](#basehelper)
  - [ConfigHelper](#confighelper)
  - [ContentHelper](#contenthelper)
  - [FrontHelper](#fronthelper)
  - [Navwalker](#navwalker)
  - [PluginContentHelper](#plugincontenthelper)
  - [WpBlocksHelper](#wpblockshelper)
- [Global Functions](#global-functions)

## Installation

```bash
composer require djanym/wphelpers
```

## Classes and Features

### AdminHelper

Customizes the WordPress admin interface.

- **`globalContentPage(array $args = [])`**: Adds a global content (options) page via ACF and an entry in the admin toolbar.
- **`hide_frontpage_featured_image()`**: Hides the featured image field on the designated front page.
- **`hide_page_cpt_featured_image()`**: Removes featured image support for all pages.
- **`hide_frontpage_editor()`**: Hides the main editor field on the front page.
- **`hide_blogpage_editor()`**: Hides the main editor field on the designated blog page.
- **`hide_page_editor(array $args = [])`**: Hides the main editor field for pages based on their template (e.g., `['template' => 'page-contact.php']`).

### BaseHelper

Enables advanced menu features using ACF.

- **`enable_mega_menu()`**: Registers ACF fields for menu items to enable MegaMenu functionality (container, column count, hide column titles) and applies corresponding CSS classes.
- **`enable_show_hide_menu_option()`**: Registers ACF fields to show or hide menu items based on the user's logged-in status.

### ConfigHelper

Site-wide configuration and optimization utilities.

- **`disable(array $features)`**: Disables various WordPress features like `emojis`, `gutenberg`, `wp_generator`, `pingback`, `comments`, etc.
- **`disableAllExcept(array $exclude)`**: Disables all optional features except the specified ones.
- **`remove_src_version($src)`**: Removes the `ver=` query string from scripts and styles.
- **`remove_unnecessary_headers($headers)`**: Cleans up HTTP headers.
- **`excerpt_length($length)`** & **`excerpt_more($more)`**: Configures post excerpt settings.
- **`remove_archive_title_prefix()`**: Removes prefixes like "Category: ", "Tag: ", etc., from archive titles.
- **`make_cpt_order_by_menu($post_type)`**: Sets a custom post type to order by `menu_order` by default in both admin and front-end.

### ContentHelper

Helpers for handling and displaying content.

- **`get_cover_image_src($post_id)`**: Retrieves the cover image URL, checking the featured image, a custom field, or a default fallback.
- **`get_cover_image()`**: Returns the full HTML `<img>` tag for the cover image.
- **`year_shortcode()`**: Adds a `[year]` shortcode to display the current year.
- **`paginator(array $args = [])`**: A highly customizable pagination function compatible with Bootstrap.

### FrontHelper

General front-end utility functions.

- **`body_class($class)`**: Easily adds a custom class to the `<body>` element.
- **`get_category_tree($args)`** & **`get_term_tree($args)`**: Retrieves a hierarchical tree of terms/categories.
- **`has_child_pages($post_id)`**: Checks if a given page has child pages.
- **`get_current_post_type()`**: Detects the post type for the current query (works for archives, singles, and taxonomies).

### Navwalker

A custom `Walker_Nav_Menu` implementation, often used for Bootstrap-compatible navigation menus.

#### Usage

```php
wp_nav_menu( [
    'menu_class'  => 'navbar-nav',
    'fallback_cb' => '\Ricubai\WPHelpers\Navwalker::fallback',
    'walker'      => new \Ricubai\WPHelpers\Navwalker(),
] );
```

### PluginContentHelper

Integrations with popular WordPress plugins.

- **`cf7_submit_gtag_event(string $send_to)`**: Adds a Google Tag Manager event for Contact Form 7 submissions.
- **`cf7_format_optgroup_for(string $field_id)`**: Adds `optgroup` support to CF7 select fields using a specific naming convention (`optgroup-` prefix).
- **`cf7_disable_autop()`**: Disables automatic paragraph formatting in Contact Form 7.
- **`is_show_toc()`**: Checks if Easy Table of Contents should be displayed on the current page.
- **`show_toc_outside_content()`**: Manually renders the TOC outside of `the_content()` using shortcodes.

### WpBlocksHelper

Customization for the Gutenberg block editor.

- **`register_block(string $block_path)`**: Registers a custom Gutenberg block.
- **`add_block_styles(string $block_name, array $args)`**: Registers custom block styles.
- **`add_editor_block_category(array $args)`**: Adds a new block category in the editor.
- **`add_editor_color_palette(array $palette)`**: Merges a custom color palette with the default WordPress editor colors.
- **`remove_block_default_styles()`**: Removes default core block styles via JavaScript.
- **`disable_editor_fullscreen()`**: Disables the block editor's fullscreen mode by default.

## Global Functions

The following functions are available globally if `WPHelpers` is active:

- **`echo_if($var, $value, $print)`**: Echoes `$print` if `$var === $value`.
- **`echo_if_else($var, $value, $print_if, $print_else)`**: Echoes `$print_if` if `$var === $value`, otherwise echoes `$print_else`.
- **`get_cf_content($cf_name, $post_id = null)`**: Retrieves a custom field value from the current post or falls back to an ACF options page.
- **`get_cover_image_src()`**: Global wrapper for `ContentHelper::get_cover_image_src()`.
- **`the_cover_image()`**: Global wrapper for `ContentHelper::get_cover_image()`.
- **`has_child_pages($post_id)`**: Global wrapper for `FrontHelper::has_child_pages()`.
