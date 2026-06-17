<?php

/**
 * Plugin Name:       RH Tracking
 * Plugin URI:        https://github.com/herbeckrobin/rh-tracking
 * Update URI:        https://github.com/herbeckrobin/rh-tracking
 * Description:       Cookieless Analytics (Umami) und client-seitiges Error-Tracking (GlitchTip, Sentry-Browser-SDK lokal gehostet). DSGVO-freundlich. Teil der rh-blueprint Kollektion.
 * Version:           0.1.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            Robin Herbeck
 * Author URI:        https://robinherbeck.de
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       rh-tracking
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

define('RHTRACKING_VERSION', '0.1.0');
define('RHTRACKING_PLUGIN_FILE', __FILE__);
define('RHTRACKING_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('RHTRACKING_PLUGIN_URL', plugin_dir_url(__FILE__));

$rhtracking_autoload = RHTRACKING_PLUGIN_DIR . 'vendor/autoload.php';

if (! is_readable($rhtracking_autoload)) {
    add_action('admin_notices', static function (): void {
        echo '<div class="notice notice-error"><p><strong>RH Tracking:</strong> Composer-Dependencies fehlen. Bitte <code>composer install</code> im Plugin-Verzeichnis ausführen.</p></div>';
    });
    return;
}

require_once $rhtracking_autoload;

RhTracking\Plugin::boot();
