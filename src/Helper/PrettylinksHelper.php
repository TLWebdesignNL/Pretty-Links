<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_prettylinks
 *
 * @copyright   Copyright (C) 2022 TLWebdesign. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE
 */

namespace TlwebNamespace\Module\Prettylinks\Site\Helper;

\defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * Helper for mod_prettylinks
 *
 * @since  1.0.0
 */
class PrettylinksHelper
{
    /**
     * URL schemes that are rendered as links; relative URLs are always allowed.
     *
     * @since  1.1.0
     */
    private const ALLOWED_SCHEMES = ['http', 'https', 'mailto', 'tel'];

    /**
     * Returns the configured links, validated and normalised. Values are not HTML-escaped; the layout does that.
     *
     * @param   Registry  $params  The module parameters
     *
     * @return  object[]  Objects with text, iconClass, url, newWindow and hideText
     *
     * @since   1.1.0
     */
    public function getLinks(Registry $params): array
    {
        $links = [];

        foreach ((array) $params->get('prettylinks', []) as $row) {
            $row       = (object) $row;
            $text      = trim((string) ($row->text ?? ''));
            $iconClass = trim(preg_replace('/[^A-Za-z0-9 _-]/', '', (string) ($row->iconclass ?? '')));
            $url       = trim((string) ($row->url ?? ''));

            if ($text === '' && $iconClass === '') {
                continue;
            }

            $links[] = (object) [
                'text'      => $text,
                'iconClass' => $iconClass,
                // A link needs text for its accessible name; icon-only rows saved by 1.0.x are rendered without a link.
                'url'       => ($url !== '' && $text !== '' && $this->isSafeUrl($url)) ? $url : '',
                'newWindow' => ($row->urltarget ?? '') === '_blank',
                'hideText'  => (string) ($row->hidemobile ?? '') === '1',
            ];
        }

        return $links;
    }

    /**
     * Only relative URLs and the allowed schemes are safe; anything else (javascript:, data:, ...) is rejected.
     *
     * @param   string  $url  The URL to check
     *
     * @return  bool
     *
     * @since   1.1.0
     */
    private function isSafeUrl(string $url): bool
    {
        // Browsers ignore control characters and whitespace inside a URL, so "java\tscript:" still runs as script.
        $url = preg_replace('/[\x00-\x20\x7F]+/', '', $url);

        if (!preg_match('~^([^/?#]*):~', $url, $matches)) {
            return true;
        }

        return \in_array(strtolower($matches[1]), self::ALLOWED_SCHEMES, true);
    }
}
