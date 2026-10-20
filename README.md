# Usf Resume — WordPress résumé/CV plugin

A practical, zero-bloat WordPress plugin that renders a full résumé/CV anywhere with one shortcode:

```
[usf_resume]
```

Manage **experience, education, skills and projects** from a single settings page in wp-admin — no page builder, no lock-in.

## Features

- One settings page (Tools → Résumé) for all resume data
- `[usf_resume]` shortcode with optional sections attribute, e.g. `[usf_resume sections="experience,skills"]`
- schema.org **Person JSON-LD** output for SEO / rich results
- Responsive, print-friendly styling scoped to the plugin (no theme conflicts)
- RTL-friendly markup
- Data stored in a single option; fully removed on uninstall
- Capability-checked (`manage_options`), nonces + sanitization on every field

## Install

1. Copy `usf-resume.php` into `wp-content/plugins/usf-resume/` (or zip the folder and upload via Plugins → Add New → Upload)
2. Activate the plugin
3. Fill your data under **Résumé** in the admin menu
4. Drop `[usf_resume]` into any page or post

## Why

Built as a real-world WordPress **plugin development** exercise by [Usf (Godde3s)](https://github.com/Godde3s) — plain PHP 8, WordPress options/settings APIs, shortcodes and hooks only. It also powers the WordPress claim on [godde3s.github.io](https://godde3s.github.io/).

## License

MIT
