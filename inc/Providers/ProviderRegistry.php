<?php

declare(strict_types=1);

namespace RhTracking\Providers;

/**
 * Hält die Analytics-Anbieter. Ein neuer Anbieter wird hier eingetragen, alles
 * andere (Reihe, Modal, Speicherung, Frontend) folgt automatisch aus dem
 * Provider-Vertrag.
 */
final class ProviderRegistry
{
    /** @var array<int, Provider>|null */
    private ?array $providers = null;

    /**
     * @return array<int, Provider>
     */
    public function all(): array
    {
        if ($this->providers === null) {
            $this->providers = [
                new Umami(),
                new Plausible(),
                new Matomo(),
                new GoatCounter(),
            ];
        }

        return $this->providers;
    }

    public function get(string $id): ?Provider
    {
        foreach ($this->all() as $provider) {
            if ($provider->id() === $id) {
                return $provider;
            }
        }

        return null;
    }

    /**
     * Konfig-Werte eines Anbieters aus der gespeicherten Option lesen.
     *
     * @return array<string, string> Feld-id => Wert.
     */
    public function values(Provider $provider): array
    {
        $values = [];
        foreach ($provider->fields() as $field) {
            $values[$field->id] = trim((string) rhbp_setting(
                'tracking',
                $provider->fieldKey($field->id),
                ''
            ));
        }

        return $values;
    }

    public function isEnabled(Provider $provider): bool
    {
        return (bool) rhbp_setting('tracking', $provider->enabledKey(), false);
    }

    /** URL des lokalen Logo-SVG. */
    public function logoUrl(Provider $provider): string
    {
        return RHTRACKING_PLUGIN_URL . 'assets/logos/' . $provider->logo();
    }
}
