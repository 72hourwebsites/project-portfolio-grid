# Project Portfolio Grid

A dependency-free WordPress plugin for a small, honest project portfolio. It registers a **Showcase Item** content type, project URL and stack fields, and the `[hw72_showcase]` shortcode.

## Requirements

- WordPress 6.4 or later
- PHP 7.4 or later

## Use

1. Install and activate the plugin.
2. Add projects under **Showcase**.
3. Add `[hw72_showcase]` to any page. The optional `limit` attribute accepts 1 to 24 items.

The plugin does not require a page builder, load third-party scripts, transmit project data, or delete content when deactivated.

## Development

The main plugin file contains registration, metadata handling, and rendering. Front-end styles live in `assets/showcase.css` and load only with the shortcode.

Check PHP syntax with:

```sh
php -l project-portfolio-grid.php
```

The code follows WordPress naming, escaping, nonce, capability, sanitization, and internationalization conventions. Run the official Plugin Check before a WordPress.org submission.

## License

GPLv2 or later. See `LICENSE`.
