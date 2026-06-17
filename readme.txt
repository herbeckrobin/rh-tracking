=== RH Tracking ===
Contributors: robinherbeck
Tags: analytics, umami, glitchtip, sentry, cookieless
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Stable tag: 0.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Cookieless analytics (Umami) and client-side error tracking (GlitchTip), both privacy friendly and without an external CDN.

== Description ==

RH Tracking adds two privacy-friendly frontend tools, each active only when configured.

= Features =

* Umami analytics: cookieless, no consent banner needed. Just set the script URL and website ID
* GlitchTip browser error tracking: reports visitor JavaScript errors. The Sentry browser SDK is served locally (no CDN), so no third-party request and no IP leak
* Environment and release fall back to sensible values (WordPress environment type, site domain) when left empty

Nothing loads in the admin, and nothing loads until the respective fields are filled in.

Part of the rh-blueprint collection. Settings live under RH Blueprint > Tracking.

== Changelog ==

= 0.1.0 =
* Initial release: Umami (cookieless) and locally-hosted GlitchTip browser error tracking.
