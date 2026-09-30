<?php

/**
 * @package        Joomla.Site
 * @subpackage     mod_prettylinks
 *
 * @copyright      Copyright (C) 2022 TLWebdesign. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

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
    <ul class="list-unstyled d-flex <?php echo $direction; ?> justify-content-<?php echo $position; ?> align-items-<?php echo $position; ?> flex-wrap gap-3 py-2 mb-0">
        <?php foreach ($data as $row) :
            $text      = trim((string) ($row->text ?? ''));
            $iconClass = trim(preg_replace('/[^A-Za-z0-9 _-]/', '', (string) ($row->iconclass ?? '')));
            $url       = trim((string) ($row->url ?? ''));
            // A link needs text for its accessible name; icon-only rows saved by 1.0.x are rendered without a link.
            $url       = ($url !== '' && $text !== '' && $isSafeUrl($url)) ? $url : '';
            $newWindow = ($row->urltarget ?? '') === '_blank';
            $hideText  = (string) ($row->hidemobile ?? '') === '1';

            if ($text !== '' || $iconClass !== '') :
                ?>
                <li class="small">
                    <?php if ($url !== '') : ?>
                        <a href="<?php echo $escape($url); ?>"<?php echo $newWindow ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
                    <?php endif; ?>
                        <?php if ($iconClass !== '') : ?>
                            <i class="<?php echo $escape($iconClass); ?> pe-2" aria-hidden="true"></i>
                        <?php endif; ?>
                        <?php if ($text !== '' && $hideText) : ?>
                            <span class="d-none d-md-inline" aria-hidden="true"><?php echo $escape($text); ?></span>
                            <span class="visually-hidden"><?php echo $escape($text); ?></span>
                        <?php elseif ($text !== '') : ?>
                            <span><?php echo $escape($text); ?></span>
                        <?php endif; ?>
                        <?php if ($url !== '' && $newWindow) : ?>
                            <span class="visually-hidden"><?php echo Text::_('MOD_PRETTYLINKS_OPENS_NEW_WINDOW'); ?></span>
                        <?php endif; ?>
                    <?php if ($url !== '') : ?>
                        </a>
                    <?php endif; ?>
                </li>
                <?php
            endif; // if $text || $iconClass
        endforeach; // foreach $data as $row
        ?>
    </ul>
    <?php
endif // check if object
?>
