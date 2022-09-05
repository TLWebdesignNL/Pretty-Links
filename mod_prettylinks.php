<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_prettylinks
 *
 * @copyright   Copyright (C) 2022 TLWebdesign. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use TlwebNamespace\Module\Prettylinks\Site\Helper\PrettylinksHelper;

$data = $params->get('prettylinks');

require ModuleHelper::getLayoutPath('mod_prettylinks', $params->get('layout', 'default'));
