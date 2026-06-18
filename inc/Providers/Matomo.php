<?php

declare(strict_types=1);

namespace RhTracking\Providers;

/**
 * Matomo: self-hosted Analytics. Das Standard-_paq-Snippet, im cookieless-Modus
 * (disableCookies vor dem ersten trackPageView), damit kein Consent nötig ist.
 */
final class Matomo extends Provider
{
    public function id(): string
    {
        return 'matomo';
    }

    public function label(): string
    {
        return 'Matomo';
    }

    public function logo(): string
    {
        return 'matomo.svg';
    }

    public function intro(): string
    {
        return __('Self-hosted, im cookieless-Modus (disableCookies). Wirkt mit Instanz-URL und Site-ID.', 'rh-tracking');
    }

    public function fields(): array
    {
        return [
            new FieldDef(
                id: 'url',
                label: __('Matomo-URL', 'rh-tracking'),
                placeholder: 'https://matomo.deine-domain.de/',
                description: __('Die Basis-URL deiner Matomo-Instanz (mit abschließendem Slash).', 'rh-tracking'),
            ),
            new FieldDef(
                id: 'site_id',
                label: __('Site-ID', 'rh-tracking'),
                placeholder: '1',
                description: __('Die idSite der Website aus Matomo.', 'rh-tracking'),
            ),
        ];
    }

    public function renderFrontend(array $values): void
    {
        $url = esc_url(trailingslashit($values['url']));
        $siteId = (string) absint($values['site_id']);

        // Standard-Matomo-Snippet, cookieless: disableCookies vor trackPageView.
        $script = sprintf(
            'var _paq=window._paq=window._paq||[];'
            . "_paq.push(['disableCookies']);"
            . "_paq.push(['trackPageView']);_paq.push(['enableLinkTracking']);"
            . "(function(){var u=%s;_paq.push(['setTrackerUrl',u+'matomo.php']);"
            . "_paq.push(['setSiteId',%s]);"
            . "var d=document,g=d.createElement('script'),s=d.getElementsByTagName('script')[0];"
            . "g.async=true;g.src=u+'matomo.js';s.parentNode.insertBefore(g,s);})();",
            wp_json_encode($url),
            wp_json_encode($siteId)
        );

        printf('<script>%s</script>' . "\n", $script); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- URL via esc_url, Site-ID via absint, beide via wp_json_encode in den JS-Kontext kodiert.
    }
}
