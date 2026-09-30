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

/**
 * Layout variables
 *
 * @var  object[]  $links           Cleaned links from PrettylinksHelper::getLinks()
 * @var  bool      $vertical        Stack the links vertically
 * @var  string    $position        start, center or end
 * @var  string    $moduleclassSfx  Module class suffix
 */

if (!$links) {
    return;
}

$escape  = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$classes = [
    'mod-prettylinks',
    $moduleclassSfx,
    'list-unstyled d-flex',
    $vertical ? 'flex-column' : 'flex-row',
    'justify-content-' . $position,
    'align-items-' . $position,
    'flex-wrap gap-3 py-2 mb-0',
];
?>
<ul class="<?php echo $escape(implode(' ', array_filter($classes))); ?>">
    <?php foreach ($links as $link) : ?>
        <li class="small">
            <?php if ($link->url !== '') : ?>
                <a href="<?php echo $escape($link->url); ?>"<?php echo $link->newWindow ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
            <?php endif; ?>
                <?php if ($link->iconClass !== '') : ?>
                    <i class="<?php echo $escape($link->iconClass); ?> pe-2" aria-hidden="true"></i>
                <?php endif; ?>
                <?php if ($link->text !== '' && $link->hideText) : ?>
                    <span class="d-none d-md-inline" aria-hidden="true"><?php echo $escape($link->text); ?></span>
                    <span class="visually-hidden"><?php echo $escape($link->text); ?></span>
                <?php elseif ($link->text !== '') : ?>
                    <span><?php echo $escape($link->text); ?></span>
                <?php endif; ?>
                <?php if ($link->url !== '' && $link->newWindow) : ?>
                    <span class="visually-hidden"><?php echo Text::_('MOD_PRETTYLINKS_OPENS_NEW_WINDOW'); ?></span>
                <?php endif; ?>
            <?php if ($link->url !== '') : ?>
                </a>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
