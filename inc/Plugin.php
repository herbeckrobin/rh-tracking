<?php

declare(strict_types=1);

namespace RhTracking;

use RhBlueprint\Core\Core;
use RhBlueprint\Core\Settings\SettingsPage;
use RhTracking\Admin\TrackingGroup;

/**
 * Bootstrap von rh-tracking.
 *
 * Hängt am Core-Hook `rh-blueprint/core/booted` (init). Registriert die Settings
 * im Tab "Tracking" und bootet das Frontend-Tracking. Braucht nur den Core.
 */
final class Plugin
{
    public static function boot(): void
    {
        if (class_exists(UpdateChecker::class)) {
            (new UpdateChecker())->boot();
        }

        add_action('rh-blueprint/core/booted', [self::class, 'onCoreBooted']);
    }

    public static function onCoreBooted(Core $core): void
    {
        $core->settings()->registerTab('tracking', __('Tracking', 'rh-tracking'), 55);
        $core->settings()->registerGroup(new TrackingGroup());

        (new Tracking())->boot();

        add_filter('rh-blueprint/dashboard/quick_links', static function (array $links): array {
            $links[] = [
                'label' => __('Tracking', 'rh-tracking'),
                'url' => admin_url('admin.php?page=' . SettingsPage::MENU_SLUG . '&tab=tracking'),
                'icon' => 'chart-bar',
            ];
            return $links;
        });
    }
}
