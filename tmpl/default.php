<?php

/**
 * @package        Joomla.Site
 * @subpackage     mod_prettylinks
 *
 * @copyright      Copyright (C) 2022 TLWebdesign. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

// Only relative URLs and these schemes are rendered as links; anything else (javascript:, data:, ...) is dropped.
$isSafeUrl = static function (string $url): bool {
    // Browsers ignore control characters and whitespace inside a URL, so "java\tscript:" still runs as script.
    $url = preg_replace('/[\x00-\x20\x7F]+/', '', $url);

    if (!preg_match('~^([^/?#]*):~', $url, $matches)) {
        return true;
    }

    return \in_array(strtolower($matches[1]), ['http', 'https', 'mailto', 'tel'], true);
};

$position  = \in_array($alignItems, ['start', 'center', 'end'], true) ? $alignItems : 'center';
$direction = (int) $verticalLayout === 1 ? 'flex-column' : 'flex-row';

if (is_object($data)) :
?>
    <div class="d-flex <?php echo $direction; ?> justify-content-<?php echo $position; ?> align-items-<?php echo $position; ?> flex-wrap py-2">
        <?php foreach ($data as $row) :
            $text      = trim((string) ($row->text ?? ''));
            $iconClass = trim(preg_replace('/[^A-Za-z0-9 _-]/', '', (string) ($row->iconclass ?? '')));
            $url       = trim((string) ($row->url ?? ''));
            $url       = ($url !== '' && $isSafeUrl($url)) ? $url : '';
            $target    = ($row->urltarget ?? '') === '_self' ? '_self' : '_blank';

            if ($text !== '' || $iconClass !== '') :
                ?>
                <span class="me-3 small">
                    <?php echo $url !== '' ? '<a target="' . $target . '" href="' . $escape($url) . '">' : ''; ?>
                        <?php echo $iconClass !== '' ? '<i class="' . $escape($iconClass) . ' pe-2"></i>' : ''; ?>
                        <span class="<?php echo (($row->hidemobile ?? '') == '1') ? 'd-none d-md-inline' : ''; ?>">
                            <?php echo $escape($text); ?>
                        </span>
                    <?php echo $url !== '' ? '</a>' : ''; ?>
                </span>
                <?php
            endif; // if $text || $iconClass
        endforeach; // foreach $data as $row
        ?>
    </div>
    <?php
endif // check if object
?>
