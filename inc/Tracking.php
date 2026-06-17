<?php

declare(strict_types=1);

namespace RhTracking;

use RhTracking\Admin\TrackingGroup;

/**
 * Frontend-Tracking: Umami (cookieless) und GlitchTip-Browser (Sentry-SDK lokal).
 *
 * Beides nur aktiv, wenn aktiviert UND konfiguriert. Das Sentry-Browser-SDK wird
 * lokal aus assets/vendor ausgeliefert (kein externes CDN, DSGVO). Im Admin wird
 * nichts geladen.
 */
final class Tracking
{
    public function boot(): void
    {
        add_action('wp_head', [$this, 'renderUmami'], 7);
        add_action('wp_enqueue_scripts', [$this, 'enqueueGlitchTip'], 5);
    }

    public function renderUmami(): void
    {
        if (is_admin()) {
            return;
        }
        if (! (bool) rhbp_setting(TrackingGroup::GROUP_ID, TrackingGroup::FIELD_UMAMI_ENABLED, false)) {
            return;
        }

        $src = trim((string) rhbp_setting(TrackingGroup::GROUP_ID, TrackingGroup::FIELD_UMAMI_SRC, ''));
        $id = trim((string) rhbp_setting(TrackingGroup::GROUP_ID, TrackingGroup::FIELD_UMAMI_WEBSITE_ID, ''));
        if ($src === '' || $id === '') {
            return;
        }

        printf(
            '<script defer src="%s" data-website-id="%s"></script>' . "\n",
            esc_url($src),
            esc_attr($id)
        );
    }

    public function enqueueGlitchTip(): void
    {
        if (! (bool) rhbp_setting(TrackingGroup::GROUP_ID, TrackingGroup::FIELD_GT_ENABLED, false)) {
            return;
        }

        $dsn = trim((string) rhbp_setting(TrackingGroup::GROUP_ID, TrackingGroup::FIELD_GT_DSN, ''));
        if ($dsn === '') {
            return;
        }

        $abs = RHTRACKING_PLUGIN_DIR . 'assets/vendor/sentry.min.js';
        if (! file_exists($abs)) {
            return;
        }

        wp_enqueue_script(
            'rh-tracking-glitchtip',
            RHTRACKING_PLUGIN_URL . 'assets/vendor/sentry.min.js',
            [],
            (string) filemtime($abs),
            ['strategy' => 'defer', 'in_footer' => false]
        );

        $environment = trim((string) rhbp_setting(TrackingGroup::GROUP_ID, TrackingGroup::FIELD_GT_ENVIRONMENT, ''));
        if ($environment === '') {
            $environment = function_exists('wp_get_environment_type') ? wp_get_environment_type() : 'production';
        }

        $release = trim((string) rhbp_setting(TrackingGroup::GROUP_ID, TrackingGroup::FIELD_GT_RELEASE, ''));
        if ($release === '') {
            $release = (string) wp_parse_url(home_url('/'), PHP_URL_HOST);
        }

        $init = sprintf(
            'if(window.Sentry){Sentry.init({dsn:%s,tracesSampleRate:0,replaysSessionSampleRate:0,environment:%s,release:%s});}',
            wp_json_encode($dsn),
            wp_json_encode($environment),
            wp_json_encode($release)
        );

        wp_add_inline_script('rh-tracking-glitchtip', $init);
    }
}
