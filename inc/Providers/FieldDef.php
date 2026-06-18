<?php

declare(strict_types=1);

namespace RhTracking\Providers;

/**
 * Definition eines Konfig-Feldes eines Anbieters (z.B. Umami-Skript-URL).
 *
 * Reines Wertobjekt. Die Anbieter geben ihre Felder darüber an, die Admin-Seite
 * rendert daraus die Inputs im Einstellungs-Modal, das Frontend liest die Werte.
 */
final readonly class FieldDef
{
    public function __construct(
        public string $id,
        public string $label,
        public string $placeholder = '',
        public string $description = '',
        public bool $required = true,
    ) {
    }
}
