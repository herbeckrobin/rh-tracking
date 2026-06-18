<?php

declare(strict_types=1);

namespace RhTracking;

use RhTracking\Providers\ProviderRegistry;

/**
 * Frontend-Analytics: rendert pro aktivem, vollständig konfiguriertem Anbieter
 * sein Tracking-Skript im <head>.
 *
 * Alle unterstützten Anbieter sind cookieless (kein Consent nötig). Im Admin
 * wird nichts geladen. Error-Tracking liegt nicht hier, sondern in rh-monitor.
 */
final class Tracking
{
    public function __construct(private readonly ProviderRegistry $registry)
    {
    }

    public function boot(): void
    {
        add_action('wp_head', [$this, 'renderProviders'], 7);
    }

    public function renderProviders(): void
    {
        if (is_admin()) {
            return;
        }

        foreach ($this->registry->all() as $provider) {
            if (! $this->registry->isEnabled($provider)) {
                continue;
            }

            $values = $this->registry->values($provider);
            if (! $provider->isConfigured($values)) {
                continue;
            }

            $provider->renderFrontend($values);
        }
    }
}
