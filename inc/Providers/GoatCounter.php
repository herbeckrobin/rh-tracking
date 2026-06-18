<?php

declare(strict_types=1);

namespace RhTracking\Providers;

/**
 * GoatCounter: minimal, cookieless. Ein async-Skript mit data-goatcounter-Endpoint.
 * Die Skript-URL zeigt idealerweise auf die self-hosted count.js (DSGVO).
 */
final class GoatCounter extends Provider
{
    public function id(): string
    {
        return 'goatcounter';
    }

    public function label(): string
    {
        return 'GoatCounter';
    }

    public function logo(): string
    {
        return 'goatcounter.svg';
    }

    public function intro(): string
    {
        return __('Minimal und cookieless. Wirkt mit Endpoint und Skript-URL.', 'rh-tracking');
    }

    public function fields(): array
    {
        return [
            new FieldDef(
                id: 'endpoint',
                label: __('Endpoint', 'rh-tracking'),
                placeholder: 'https://DEINCODE.goatcounter.com/count',
                description: __('Die data-goatcounter-Zähl-URL deiner GoatCounter-Instanz.', 'rh-tracking'),
            ),
            new FieldDef(
                id: 'src',
                label: __('Skript-URL', 'rh-tracking'),
                placeholder: 'https://deine-domain.de/count.js',
                description: __('Die count.js-URL. Für DSGVO selbst hosten statt //gc.zgo.at/count.js.', 'rh-tracking'),
            ),
        ];
    }

    public function renderFrontend(array $values): void
    {
        printf(
            '<script data-goatcounter="%s" async src="%s"></script>' . "\n",
            esc_url($values['endpoint']),
            esc_url($values['src'])
        );
    }
}
