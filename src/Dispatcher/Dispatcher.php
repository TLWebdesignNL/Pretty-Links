<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_prettylinks
 *
 * @copyright   Copyright (C) 2022 TLWebdesign. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TlwebNamespace\Module\Prettylinks\Site\Dispatcher;

\defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;

/**
 * Dispatcher for mod_prettylinks
 *
 * @since  1.1.0
 */
class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
    use HelperFactoryAwareTrait;

    /**
     * Returns the layout data: cleaned links and whitelisted display options.
     *
     * @return  array
     *
     * @since   1.1.0
     */
    protected function getLayoutData(): array
    {
        $data   = parent::getLayoutData();
        $params = $data['params'];
        $links  = $this->getHelperFactory()->getHelper('PrettylinksHelper')->getLinks($params);
        $pos    = (string) $params->get('linksposition', 'center');

        $data['links']          = $links;
        $data['vertical']       = (int) $params->get('vertical', 0) === 1;
        $data['position']       = \in_array($pos, ['start', 'center', 'end'], true) ? $pos : 'center';
        $data['moduleclassSfx'] = trim((string) $params->get('moduleclass_sfx', ''));

        foreach ($links as $link) {
            if ($link->iconClass !== '') {
                $this->getApplication()->getDocument()->getWebAssetManager()->useStyle('fontawesome');
                break;
            }
        }

        return $data;
    }
}
