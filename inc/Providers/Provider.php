<?php

declare(strict_types=1);

namespace RhTracking\Providers;

/**
 * Ein Analytics-Anbieter (Umami, Plausible, Matomo, GoatCounter).
 *
 * Jeder Anbieter ist eine eigene Klasse: Metadaten + Konfig-Felder + sein
 * Frontend-Skript. Ein neuer Anbieter ist damit eine Hinzufügung (neue Klasse,
 * ein Eintrag in der Registry), kein Umbau. Die Werte aller Anbieter liegen in
 * EINER Option (rhbp_settings_tracking), die Feld-Keys sind mit dem Anbieter-Slug
 * präfixt ({slug}_enabled, {slug}_{feldId}), damit rhbp_setting() weiter greift.
 */
abstract class Provider
{
    /** Eindeutiger Slug, Präfix aller Feld-Keys (z.B. "umami"). */
    abstract public function id(): string;

    /** Anzeigename in der Reihe und im Modal. */
    abstract public function label(): string;

    /** Dateiname des lokalen Logo-SVG in assets/logos/. */
    abstract public function logo(): string;

    /** Kurzer Satz im Konfig-Modal (was der Anbieter ist). */
    abstract public function intro(): string;

    /**
     * Konfig-Felder dieses Anbieters.
     *
     * @return array<int, FieldDef>
     */
    abstract public function fields(): array;

    /**
     * Gibt das Frontend-Tracking-Skript aus. Wird nur aufgerufen, wenn der
     * Anbieter aktiviert UND vollständig konfiguriert ist.
     *
     * @param array<string, string> $values Konfig-Werte (Feld-id => Wert).
     */
    abstract public function renderFrontend(array $values): void;

    public function enabledKey(): string
    {
        return $this->id() . '_enabled';
    }

    public function fieldKey(string $fieldId): string
    {
        return $this->id() . '_' . $fieldId;
    }

    /**
     * Vollständig konfiguriert = alle Pflichtfelder gesetzt.
     *
     * @param array<string, string> $values
     */
    public function isConfigured(array $values): bool
    {
        foreach ($this->fields() as $field) {
            if ($field->required && trim((string) ($values[$field->id] ?? '')) === '') {
                return false;
            }
        }

        return true;
    }
}
