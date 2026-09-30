<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_prettylinks
 *
 * @copyright   Copyright (C) 2022 TLWebdesign. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Extension\Service\Provider\HelperFactory;
use Joomla\CMS\Extension\Service\Provider\Module;
use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class () implements ServiceProviderInterface {
    public function register(Container $container): void
    {
        $container->registerServiceProvider(new ModuleDispatcherFactory('\\TlwebNamespace\\Module\\Prettylinks'));
        $container->registerServiceProvider(new HelperFactory('\\TlwebNamespace\\Module\\Prettylinks\\Site\\Helper'));
        $container->registerServiceProvider(new Module());
    }
};
