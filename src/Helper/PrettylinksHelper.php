<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_prettylinks
 *
 * @copyright   Copyright (C) 2022 TLWebdesign. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TlwebNamespace\Module\Prettylinks\Site\Helper;

\defined('_JEXEC') or die;

/**
 * Helper for mod_prettylinks
 *
 * @since  V1.0.0
 */
class PrettylinksHelper
{
    /**
     * Retrieve Prettybuttons test
     *
     * @param   Registry        $params  The module parameters
     * @param   CMSApplication  $app     The application
     *
     * @return  array
     */
    public static function getText()
    {
        return 'PrettylinksHelpertest';
    }
}
