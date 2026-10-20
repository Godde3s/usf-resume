=== Usf Résumé ===
Contributors: godde3s
Tags: resume, cv, portfolio, shortcode, schema
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: MIT
License URI: https://opensource.org/licenses/MIT

A practical résumé/CV plugin — manage everything from one settings page and render it anywhere with the [usf_resume] shortcode.

== Description ==

Usf Résumé adds a "Résumé" screen to wp-admin where you keep your personal info, experience, education, projects and skills. Drop the `[usf_resume]` shortcode on any page or post and it renders a clean, responsive, print-friendly résumé — no page builder required.

Features:

* One settings page for the whole résumé (Résumé → Edit résumé)
* Simple line format: `Period | Title | Organization | Details`
* Skills with levels: `Skill | Level`
* Single-section shortcodes, e.g. `[usf_resume section="skills"]`
* schema.org Person JSON-LD for SEO (name, job title, skills, location)
* Sanitized input, escaped output, uninstall cleanup
* Zero bloat: one file, no database tables, no external assets

== Installation ==

1. Upload the `usf-resume` folder to `/wp-content/plugins/` (or upload the zip via Plugins → Add New → Upload Plugin).
2. Activate the plugin.
3. Open the "Résumé" menu in wp-admin and fill in your data.
4. Place `[usf_resume]` on any page.

== Frequently Asked Questions ==

= Can I show just one section? =

Yes — `[usf_resume section="experience"]` (also: education, projects, skills).

= Can I use links in the Details column? =

Yes, basic HTML is allowed there: `<a>`, `<strong>`, `<em>`, `<br>`.

== Changelog ==

= 1.0.0 =
* First release.
