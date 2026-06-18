# RH Tracking

Cookieless Analytics für WordPress: Umami, Plausible, Matomo und GoatCounter. Teil der rh-blueprint Kollektion.

Jeder Anbieter ist eine eigene Reihe mit Logo, Status und An/Aus-Schalter. Aktiv nur, wenn aktiviert und konfiguriert. Im Admin wird nichts geladen. Error-Tracking (GlitchTip) liegt nicht hier, sondern im Modul `rh-monitor`.

## Anbieter

- **Umami**: cookieless. Skript-URL und Website-ID.
- **Plausible**: cookieless. Domain und Skript-URL (self-hosted oder plausible.io).
- **Matomo**: self-hosted, im cookieless-Modus (`disableCookies`). Instanz-URL und Site-ID.
- **GoatCounter**: minimal, cookieless. Endpoint und Skript-URL.

Alle sind cookieless, kein Consent-Banner nötig. Ein neuer Anbieter ist eine eigene Provider-Klasse plus ein Eintrag in der Registry, der Rest (Reihe, Modal, Speicherung, Frontend) folgt automatisch.

## Einstellungen

Im Backend unter **RH Blueprint → Tracking**: Anbieter über den Schalter aktivieren, über das Zahnrad konfigurieren. Die Status-Pill zeigt aktiv, unvollständig oder inaktiv.

## DSGVO

Alle Anbieter sind cookieless und ohne Einwilligung nutzbar, sofern self-hosted bzw. EU-konform betrieben. Skript-URLs idealerweise selbst hosten (kein Drittanbieter-Request, kein IP-Leak). Wird ein einwilligungspflichtiger Dienst ergänzt, das Modul `rh-consent` nutzen.

## Installation

ZIP hochladen und aktivieren. Der geteilte Core ist gebündelt.

## Voraussetzungen

WordPress 6.5+, PHP 8.1+.
