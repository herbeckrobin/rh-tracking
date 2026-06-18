=== RH Tracking ===
Contributors: robinherbeck
Tags: analytics, umami, plausible, matomo, cookieless
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Stable tag: 0.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Cookieless analytics for WordPress: Umami, Plausible, Matomo and GoatCounter, each as its own provider row.

== Description ==

RH Tracking adds cookieless analytics providers, each as its own row with logo, status and an on/off switch. Configuration lives in a per-provider modal. A provider is active only when enabled and fully configured.

= Providers =

* Umami: cookieless. Script URL and website ID
* Plausible: cookieless. Domain and script URL (self-hosted or plausible.io)
* Matomo: self-hosted, cookieless mode (disableCookies). Instance URL and site ID
* GoatCounter: minimal, cookieless. Endpoint and script URL

All providers are cookieless, so no consent banner is required. Nothing loads in the admin, and nothing loads until a provider is enabled and configured. Adding a provider is a new provider class plus one registry entry.

Error tracking (GlitchTip) is not part of this plugin. It lives in the rh-monitor module.

Part of the rh-blueprint collection. Settings live under RH Blueprint > Tracking.

== Changelog ==

= 0.2.0 =
* Rebuilt as provider rows: Umami, Plausible, Matomo and GoatCounter, each with logo, status and config modal.
* Browser error tracking (GlitchTip) moved to the rh-monitor module.

= 0.1.0 =
* Initial release: Umami (cookieless) and locally-hosted GlitchTip browser error tracking.
