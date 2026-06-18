<?php

declare(strict_types=1);

namespace RhTracking\Admin;

use RhBlueprint\Core\Settings\SettingsPage;
use RhTracking\Providers\Provider;
use RhTracking\Providers\ProviderRegistry;

/**
 * Rendert den Tracking-Tab als Anbieter-Reihen (Umami, Plausible, Matomo,
 * GoatCounter) im Look der Sync-Seite: pro Anbieter eine Reihe mit Logo, Status
 * und Toggle, die Konfig-Felder liegen im Einstellungs-Modal (Zahnrad).
 *
 * Der Tab registriert KEINE GroupInterface (sonst würde der Core ein flaches
 * Feld-Formular rendern). Stattdessen eigener Content über die tab_content-Hooks
 * und eigene admin-post-Handler, die in dieselbe Option `rhbp_settings_tracking`
 * schreiben, aus der das Frontend (und rhbp_setting) liest.
 */
final class TrackingProvidersPage
{
    public const TAB_ID = 'tracking';
    public const CAPABILITY = 'manage_options';
    public const NONCE_TOGGLE = 'rhbp_tracking_toggle';
    public const NONCE_SAVE = 'rhbp_tracking_save';

    public function __construct(private readonly ProviderRegistry $registry)
    {
    }

    public function boot(): void
    {
        add_action('rh-blueprint/settings/tab_content_before', [$this, 'renderInlineMessage']);
        add_action('rh-blueprint/settings/tab_content_after', [$this, 'renderProviders']);
        add_action('admin_post_rhbp_tracking_toggle', [$this, 'handleToggle']);
        add_action('admin_post_rhbp_tracking_save', [$this, 'handleSave']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    public function enqueueAssets(string $hook): void
    {
        $page = isset($_GET['page']) ? sanitize_key((string) $_GET['page']) : '';
        if ($page !== SettingsPage::MENU_SLUG) {
            return;
        }

        $abs = RHTRACKING_PLUGIN_DIR . 'assets/admin.css';
        if (! file_exists($abs)) {
            return;
        }

        wp_enqueue_style(
            'rh-tracking-admin',
            RHTRACKING_PLUGIN_URL . 'assets/admin.css',
            ['rh-blueprint-settings'],
            (string) filemtime($abs)
        );
    }

    public function renderInlineMessage(string $tabId): void
    {
        if ($tabId !== self::TAB_ID) {
            return;
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- nur Anzeige einer Status-Meldung nach Redirect, keine zustandsändernde Aktion.
        $message = isset($_GET['rhbp_message']) ? sanitize_key(wp_unslash($_GET['rhbp_message'])) : '';
        if ($message === '') {
            return;
        }

        $map = [
            'tracking_saved' => ['success', __('Einstellungen wurden gespeichert.', 'rh-tracking')],
            'tracking_enabled' => ['success', __('Anbieter wurde aktiviert.', 'rh-tracking')],
            'tracking_disabled' => ['success', __('Anbieter wurde deaktiviert.', 'rh-tracking')],
            'tracking_unknown' => ['warning', __('Unbekannter Anbieter.', 'rh-tracking')],
        ];

        if (! isset($map[$message])) {
            return;
        }

        [$type, $text] = $map[$message];
        printf(
            '<div class="rhbp-callout rhbp-callout--%s">%s</div>',
            esc_attr($type === 'success' ? 'success' : 'warn'),
            esc_html($text)
        );
    }

    public function renderProviders(string $tabId): void
    {
        if ($tabId !== self::TAB_ID) {
            return;
        }

        echo '<div class="rhbp-providers">';
        echo '<p class="rhbp-providers__intro">';
        echo esc_html__('Aktiviere die Analytics-Anbieter, die du nutzen willst. Alle sind cookieless, kein Consent-Banner nötig. Konfiguration über das Zahnrad. Error-Tracking findest du im Tab Monitoring.', 'rh-tracking');
        echo '</p>';

        echo '<div class="rhbp-providers__list">';
        foreach ($this->registry->all() as $provider) {
            $this->renderRow($provider);
        }
        echo '</div>';
        echo '</div>';

        // Modals außerhalb der Reihen-Liste (eigene Overlay-Ebene).
        foreach ($this->registry->all() as $provider) {
            $this->renderModal($provider);
        }
    }

    private function renderRow(Provider $provider): void
    {
        $enabled = $this->registry->isEnabled($provider);
        $configured = $provider->isConfigured($this->registry->values($provider));
        $modalId = 'rhbp-modal-provider-' . $provider->id();

        echo '<div class="rhbp-card rhbp-provider">';

        // Logo + Name
        echo '<div class="rhbp-provider__brand">';
        printf(
            '<img class="rhbp-provider__logo" src="%s" alt="%s" width="28" height="28" />',
            esc_url($this->registry->logoUrl($provider)),
            esc_attr($provider->label())
        );
        echo '<strong class="rhbp-provider__name">' . esc_html($provider->label()) . '</strong>';
        echo '</div>';

        // Status-Pill
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pill-Markup in statusPill() bereits escapt.
        echo $this->statusPill($enabled, $configured);

        // Aktionen: Toggle (auto-submit) + Zahnrad
        echo '<div class="rhbp-provider__actions">';

        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" class="rhbp-toggle-form">';
        wp_nonce_field(self::NONCE_TOGGLE);
        echo '<input type="hidden" name="action" value="rhbp_tracking_toggle" />';
        echo '<input type="hidden" name="provider" value="' . esc_attr($provider->id()) . '" />';
        printf(
            '<label class="rhbp-switch" title="%s"><input type="checkbox" name="enabled" value="1" %s onchange="this.form.submit()" /><span class="rhbp-switch__track" aria-hidden="true"></span></label>',
            esc_attr($enabled ? __('Anbieter deaktivieren', 'rh-tracking') : __('Anbieter aktivieren', 'rh-tracking')),
            checked($enabled, true, false)
        );
        echo '</form>';

        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Attribute via esc_attr, Icon ist internes SVG aus festen Konstanten.
        echo '<button type="button" class="rhbp-btn rhbp-btn--ghost rhbp-btn--icon" data-rhbp-modal-open="' . esc_attr($modalId) . '" title="' . esc_attr__('Konfigurieren', 'rh-tracking') . '" aria-label="' . esc_attr__('Konfigurieren', 'rh-tracking') . '">' . $this->icon('gear') . '</button>';

        echo '</div>';
        echo '</div>'; // .rhbp-provider
    }

    private function statusPill(bool $enabled, bool $configured): string
    {
        if (! $enabled) {
            return '<span class="rhbp-pill">' . esc_html__('Inaktiv', 'rh-tracking') . '</span>';
        }
        if (! $configured) {
            return '<span class="rhbp-pill rhbp-pill--warn">' . esc_html__('Unvollständig', 'rh-tracking') . '</span>';
        }

        return '<span class="rhbp-pill rhbp-pill--ok"><span class="rhbp-pill__dot" aria-hidden="true"></span> ' . esc_html__('Aktiv', 'rh-tracking') . '</span>';
    }

    private function renderModal(Provider $provider): void
    {
        $modalId = 'rhbp-modal-provider-' . $provider->id();
        $values = $this->registry->values($provider);

        echo '<div class="rhbp-modal-backdrop" id="' . esc_attr($modalId) . '" data-rhbp-modal-backdrop>';
        echo '<div class="rhbp-modal" role="dialog" aria-modal="true" aria-label="' . esc_attr(sprintf(/* translators: %s: provider name */ __('Einstellungen für %s', 'rh-tracking'), $provider->label())) . '">';

        // Kopf
        echo '<div class="rhbp-modal__head">';
        echo '<div class="rhbp-modal__head-l">';
        printf(
            '<img class="rhbp-provider__logo" src="%s" alt="" width="24" height="24" />',
            esc_url($this->registry->logoUrl($provider))
        );
        echo '<div>';
        echo '<h3 class="rhbp-modal__title">' . esc_html($provider->label()) . '</h3>';
        echo '<p class="rhbp-modal__sub">' . esc_html($provider->intro()) . '</p>';
        echo '</div>';
        echo '</div>';
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Icon ist internes SVG aus festen Konstanten.
        echo '<button type="button" class="rhbp-btn rhbp-btn--ghost rhbp-btn--icon" data-rhbp-modal-close aria-label="' . esc_attr__('Schließen', 'rh-tracking') . '">' . $this->icon('close') . '</button>';
        echo '</div>';

        // Body: Konfig-Felder
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        wp_nonce_field(self::NONCE_SAVE);
        echo '<input type="hidden" name="action" value="rhbp_tracking_save" />';
        echo '<input type="hidden" name="provider" value="' . esc_attr($provider->id()) . '" />';

        echo '<div class="rhbp-modal__body">';
        foreach ($provider->fields() as $field) {
            $inputId = 'rhbp-' . $provider->id() . '-' . $field->id;
            echo '<div class="rhbp-field">';
            echo '<label class="rhbp-field__label" for="' . esc_attr($inputId) . '">' . esc_html($field->label) . '</label>';
            printf(
                '<input type="text" id="%s" name="%s" value="%s" placeholder="%s" class="regular-text" />',
                esc_attr($inputId),
                esc_attr($field->id),
                esc_attr($values[$field->id] ?? ''),
                esc_attr($field->placeholder)
            );
            if ($field->description !== '') {
                echo '<p class="rhbp-field__desc">' . esc_html($field->description) . '</p>';
            }
            echo '</div>';
        }
        echo '</div>'; // body

        echo '<div class="rhbp-modal__foot">';
        echo '<button type="button" class="rhbp-btn rhbp-btn--ghost" data-rhbp-modal-close>' . esc_html__('Abbrechen', 'rh-tracking') . '</button>';
        echo '<button type="submit" class="rhbp-btn rhbp-btn--primary">' . esc_html__('Speichern', 'rh-tracking') . '</button>';
        echo '</div>';

        echo '</form>';
        echo '</div>'; // modal
        echo '</div>'; // backdrop
    }

    public function handleToggle(): void
    {
        if (! current_user_can(self::CAPABILITY)) {
            wp_die(esc_html__('Keine Berechtigung.', 'rh-tracking'));
        }
        check_admin_referer(self::NONCE_TOGGLE);

        $providerId = isset($_POST['provider']) ? sanitize_key(wp_unslash($_POST['provider'])) : '';
        $provider = $this->registry->get($providerId);
        if ($provider === null) {
            $this->redirect('tracking_unknown');
        }

        $enabled = isset($_POST['enabled']);
        rhbp_update_setting('tracking', $provider->enabledKey(), $enabled);

        $this->redirect($enabled ? 'tracking_enabled' : 'tracking_disabled');
    }

    public function handleSave(): void
    {
        if (! current_user_can(self::CAPABILITY)) {
            wp_die(esc_html__('Keine Berechtigung.', 'rh-tracking'));
        }
        check_admin_referer(self::NONCE_SAVE);

        $providerId = isset($_POST['provider']) ? sanitize_key(wp_unslash($_POST['provider'])) : '';
        $provider = $this->registry->get($providerId);
        if ($provider === null) {
            $this->redirect('tracking_unknown');
        }

        $values = [];
        foreach ($provider->fields() as $field) {
            $raw = isset($_POST[$field->id]) ? wp_unslash($_POST[$field->id]) : '';
            $values[$provider->fieldKey($field->id)] = sanitize_text_field((string) $raw);
        }
        rhbp_update_settings('tracking', $values);

        $this->redirect('tracking_saved');
    }

    /**
     * @param string $message Message-Key für die Inline-Meldung.
     */
    private function redirect(string $message): never
    {
        $url = add_query_arg(
            [
                'page' => SettingsPage::MENU_SLUG,
                'tab' => self::TAB_ID,
                'rhbp_message' => $message,
            ],
            admin_url('admin.php')
        );
        wp_safe_redirect($url);
        exit;
    }

    private function icon(string $name): string
    {
        $paths = [
            'gear' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>',
            'close' => '<path d="M6 6l12 12M18 6L6 18"/>',
        ];

        $path = $paths[$name] ?? '';

        return '<svg class="rhbp-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $path . '</svg>';
    }
}
