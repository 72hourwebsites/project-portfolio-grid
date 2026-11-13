=== Project Portfolio Grid ===
Contributors: dominiiking
Tags: portfolio, showcase, projects, shortcode
Requires at least: 6.4
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A small portfolio custom post type with project details and an accessible shortcode grid.

== Description ==

Project Portfolio Grid adds a Showcase Items post type for portfolio projects. Each item supports a title, description, excerpt, featured image, project URL, and stack or services summary.

The included shortcode renders a responsive grid with escaped output and no external front-end dependencies. Styles load only when the shortcode is present.

== Installation ==

1. Upload the `project-portfolio-grid` folder to `/wp-content/plugins/`, or install a ZIP of this repository through the WordPress plugin uploader.
2. Activate **Project Portfolio Grid**.
3. Add entries under **Showcase** in the WordPress admin.
4. Place `[hw72_showcase]` in a page or post. Use `[hw72_showcase limit="12"]` to show up to 12 items; the allowed range is 1 to 24.

== Frequently Asked Questions ==

= Does this plugin require a page builder? =

No. It uses native WordPress post types, metadata, templates, and shortcodes.

= Does deactivating the plugin delete showcase items? =

No. Deactivation and uninstallation do not delete project content.

= Does the plugin send data to another service? =

No. It makes no external API requests and adds no tracking.

== Changelog ==

= 1.1.0 =
* Renamed to Project Portfolio Grid (slug project-portfolio-grid) for the WordPress.org directory. Post type, meta keys and the [hw72_showcase] shortcode are unchanged, so existing items keep working.

= 1.0.1 =
* Fix: the project URL and stack fields now save through the block editor and the REST API (post type declares custom-fields support).

= 1.0.0 =
* Initial release with Showcase Items, project metadata, and the responsive shortcode grid.

== License ==

Copyright 2026 Angel Lopez.

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
