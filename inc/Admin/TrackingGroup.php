<?php

declare(strict_types=1);

namespace RhTracking\Admin;

use RhBlueprint\Core\Settings\GroupInterface;
use RhBlueprint\Core\Settings\SettingField;

/**
 * Settings-Gruppe fürs Frontend-Tracking.
 *
 * Beides ist erst aktiv, wenn die jeweiligen Felder gesetzt sind. Umami ist
 * cookieless (kein Consent nötig), GlitchTip läuft client-seitig über das lokal
 * gehostete Sentry-Browser-SDK (kein CDN, DSGVO).
 */
final class TrackingGroup implements GroupInterface
{
    public const GROUP_ID = 'tracking';

    public const FIELD_UMAMI_ENABLED = 'umami_enabled';
    public const FIELD_UMAMI_SRC = 'umami_src';
    public const FIELD_UMAMI_WEBSITE_ID = 'umami_website_id';

    public const FIELD_GT_ENABLED = 'glitchtip_enabled';
    public const FIELD_GT_DSN = 'glitchtip_dsn';
    public const FIELD_GT_ENVIRONMENT = 'glitchtip_environment';
    public const FIELD_GT_RELEASE = 'glitchtip_release';

    public function id(): string
    {
        return self::GROUP_ID;
    }

    public function tab(): string
    {
        return 'tracking';
    }

    public function title(): string
    {
        return __('Tracking', 'rh-tracking');
    }

    public function description(): string
    {
        return __('Cookieless Analytics und client-seitiges Error-Tracking, beides datensparsam und ohne externes CDN.', 'rh-tracking');
    }

    public function fields(): array
    {
        return [
            new SettingField(
                id: self::FIELD_UMAMI_ENABLED,
                type: SettingField::TYPE_BOOLEAN,
                label: __('Umami Analytics aktivieren', 'rh-tracking'),
                description: __('Cookieless, kein Consent-Banner nötig. Wirkt nur mit Skript-URL und Website-ID.', 'rh-tracking'),
                default: false,
                keywords: ['umami', 'analytics', 'statistik', 'cookieless'],
            ),
            new SettingField(
                id: self::FIELD_UMAMI_SRC,
                type: SettingField::TYPE_URL,
                label: __('Umami Skript-URL', 'rh-tracking'),
                description: __('z.B. https://analytics.deine-domain.de/script.js', 'rh-tracking'),
                default: '',
                keywords: ['umami', 'script', 'url'],
            ),
            new SettingField(
                id: self::FIELD_UMAMI_WEBSITE_ID,
                type: SettingField::TYPE_TEXT,
                label: __('Umami Website-ID', 'rh-tracking'),
                description: __('Die UUID der Website aus dem Umami-Dashboard.', 'rh-tracking'),
                default: '',
                keywords: ['umami', 'website', 'id', 'uuid'],
            ),
            new SettingField(
                id: self::FIELD_GT_ENABLED,
                type: SettingField::TYPE_BOOLEAN,
                label: __('GlitchTip (Browser) aktivieren', 'rh-tracking'),
                description: __('Meldet JavaScript-Fehler der Besucher an GlitchTip. Sentry-Browser-SDK wird lokal ausgeliefert (kein CDN). Wirkt nur mit DSN.', 'rh-tracking'),
                default: false,
                keywords: ['glitchtip', 'sentry', 'javascript', 'error', 'browser'],
            ),
            new SettingField(
                id: self::FIELD_GT_DSN,
                type: SettingField::TYPE_URL,
                label: __('GlitchTip DSN (Browser)', 'rh-tracking'),
                description: __('Die DSN deines GlitchTip-Projekts. Leer = aus.', 'rh-tracking'),
                default: '',
                keywords: ['glitchtip', 'dsn', 'sentry'],
            ),
            new SettingField(
                id: self::FIELD_GT_ENVIRONMENT,
                type: SettingField::TYPE_TEXT,
                label: __('Environment', 'rh-tracking'),
                description: __('Leer = automatisch (WordPress-Umgebungstyp).', 'rh-tracking'),
                default: '',
                keywords: ['environment', 'umgebung'],
            ),
            new SettingField(
                id: self::FIELD_GT_RELEASE,
                type: SettingField::TYPE_TEXT,
                label: __('Release', 'rh-tracking'),
                description: __('Leer = automatisch (Domain).', 'rh-tracking'),
                default: '',
                keywords: ['release', 'version'],
            ),
        ];
    }
}
