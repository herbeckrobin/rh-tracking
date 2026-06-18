<?php

declare(strict_types=1);

namespace RhTracking\Providers;

/**
 * Umami: cookieless Analytics. Ein einzelnes defer-Skript mit Website-ID.
 */
final class Umami extends Provider
{
    public function id(): string
    {
        return 'umami';
    }

    public function label(): string
    {
        return 'Umami';
    }

    public function logo(): string
    {
        return 'umami.svg';
    }

    public function intro(): string
    {
        return __('Cookieless, kein Consent-Banner nötig. Wirkt mit Skript-URL und Website-ID.', 'rh-tracking');
    }

    public function fields(): array
    {
        return [
            new FieldDef(
                id: 'src',
                label: __('Skript-URL', 'rh-tracking'),
                placeholder: 'https://analytics.deine-domain.de/script.js',
                description: __('Die script.js-URL deiner Umami-Instanz.', 'rh-tracking'),
            ),
            new FieldDef(
                id: 'website_id',
                label: __('Website-ID', 'rh-tracking'),
                placeholder: 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx',
                description: __('Die UUID der Website aus dem Umami-Dashboard.', 'rh-tracking'),
            ),
        ];
    }

    public function renderFrontend(array $values): void
    {
        printf(
            '<script defer src="%s" data-website-id="%s"></script>' . "\n",
            esc_url($values['src']),
            esc_attr($values['website_id'])
        );
    }
}
