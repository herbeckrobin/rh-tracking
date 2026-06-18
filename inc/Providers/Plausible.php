<?php

declare(strict_types=1);

namespace RhTracking\Providers;

/**
 * Plausible: cookieless Analytics. Defer-Skript mit data-domain. Die Skript-URL
 * zeigt auf die self-hosted Instanz (oder plausible.io).
 */
final class Plausible extends Provider
{
    public function id(): string
    {
        return 'plausible';
    }

    public function label(): string
    {
        return 'Plausible';
    }

    public function logo(): string
    {
        return 'plausible.svg';
    }

    public function intro(): string
    {
        return __('Cookieless, datensparsam, kein Consent nötig. Wirkt mit Domain und Skript-URL.', 'rh-tracking');
    }

    public function fields(): array
    {
        return [
            new FieldDef(
                id: 'domain',
                label: __('Domain', 'rh-tracking'),
                placeholder: 'deine-domain.de',
                description: __('Die in Plausible angelegte Domain (ohne https://). Wird als data-domain gesetzt.', 'rh-tracking'),
            ),
            new FieldDef(
                id: 'src',
                label: __('Skript-URL', 'rh-tracking'),
                placeholder: 'https://plausible.deine-domain.de/js/script.js',
                description: __('Die script.js-URL deiner Plausible-Instanz (self-hosted oder plausible.io).', 'rh-tracking'),
            ),
        ];
    }

    public function renderFrontend(array $values): void
    {
        printf(
            '<script defer data-domain="%s" src="%s"></script>' . "\n",
            esc_attr($values['domain']),
            esc_url($values['src'])
        );
    }
}
