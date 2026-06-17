# RH Tracking

Cookieless Analytics (Umami) und client-seitiges Error-Tracking (GlitchTip). Teil der rh-blueprint Kollektion.

Zwei datensparsame Frontend-Werkzeuge, jeweils nur aktiv, wenn konfiguriert. Im Admin wird nichts geladen.

## Was es macht

- **Umami Analytics**: cookieless, kein Consent-Banner nötig. Nur Skript-URL und Website-ID setzen.
- **GlitchTip im Browser**: meldet JavaScript-Fehler der Besucher. Das Sentry-Browser-SDK wird **lokal** ausgeliefert (kein CDN), also kein Drittanbieter-Request und kein IP-Leak.
- **Sinnvolle Defaults**: Environment fällt auf den WordPress-Umgebungstyp zurück, Release auf die Domain.

## Einstellungen

Im Backend unter **RH Blueprint → Tracking**:

- **Umami**: aktivieren, Skript-URL (z.B. `https://analytics.deine-domain.de/script.js`), Website-ID.
- **GlitchTip (Browser)**: aktivieren, DSN, Environment, Release.

## DSGVO

Umami ist cookieless (keine Einwilligung nötig), GlitchTip läuft self-hosted ohne Cookies, das SDK lokal gehostet. In diesem Standard-Setup ist kein Consent-Banner erforderlich. Wird ein einwilligungspflichtiger Dienst ergänzt, das Modul `rh-consent` nutzen.

## Installation

ZIP hochladen und aktivieren. Der geteilte Core ist gebündelt.

## Voraussetzungen

WordPress 6.5+, PHP 8.1+.
