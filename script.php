<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_prettylinks
 *
 * @copyright   Copyright (C) 2022 TLWebdesign. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\Database\DatabaseInterface;
use Joomla\Filesystem\File;
use Joomla\Registry\Registry;

return new class () implements InstallerScriptInterface {
    private const MINIMUM_JOOMLA = '5.4.0';

    private const MINIMUM_PHP = '8.1.0';

    public function install(InstallerAdapter $adapter): bool
    {
        echo Text::_('MOD_PRETTYLINKS_INSTALLERSCRIPT_INSTALL');

        return true;
    }

    public function update(InstallerAdapter $adapter): bool
    {
        echo Text::_('MOD_PRETTYLINKS_INSTALLERSCRIPT_UPDATE');

        return true;
    }

    public function uninstall(InstallerAdapter $adapter): bool
    {
        echo Text::_('MOD_PRETTYLINKS_INSTALLERSCRIPT_UNINSTALL');

        return true;
    }

    public function preflight(string $type, InstallerAdapter $adapter): bool
    {
        if ($type === 'uninstall') {
            return true;
        }

        if (version_compare(PHP_VERSION, self::MINIMUM_PHP, '<')) {
            Log::add(Text::sprintf('JLIB_INSTALLER_MINIMUM_PHP', self::MINIMUM_PHP), Log::WARNING, 'jerror');

            return false;
        }

        if (version_compare(JVERSION, self::MINIMUM_JOOMLA, '<')) {
            Log::add(Text::sprintf('JLIB_INSTALLER_MINIMUM_JOOMLA', self::MINIMUM_JOOMLA), Log::WARNING, 'jerror');

            return false;
        }

        return true;
    }

    public function postflight(string $type, InstallerAdapter $adapter): bool
    {
        if ($type === 'update') {
            $this->removeLegacyEntryFile();
            $this->warnAboutIconOnlyLinks();
        }

        return true;
    }

    /**
     * 1.0.x used a mod_prettylinks.php entry file; since 1.1.0 the module boots from services/provider.php.
     */
    private function removeLegacyEntryFile(): void
    {
        $file = JPATH_SITE . '/modules/mod_prettylinks/mod_prettylinks.php';

        if (is_file($file)) {
            File::delete($file);
        }
    }

    /**
     * Warn about modules with icon-only links: since 1.1.0 a link needs text for its accessible name,
     * so these rows are rendered as an icon without a link until text is added.
     */
    private function warnAboutIconOnlyLinks(): void
    {
        try {
            $db     = Factory::getContainer()->get(DatabaseInterface::class);
            $module = 'mod_prettylinks';
            $query  = $db->getQuery(true)
                ->select($db->quoteName(['id', 'title', 'params']))
                ->from($db->quoteName('#__modules'))
                ->where($db->quoteName('module') . ' = :module')
                ->where($db->quoteName('published') . ' != -2')
                ->bind(':module', $module);

            $affected = [];

            foreach ($db->setQuery($query)->loadObjectList() as $item) {
                $links = (new Registry($item->params))->get('prettylinks');

                foreach ((array) $links as $link) {
                    $link = (object) $link;

                    if (trim((string) ($link->text ?? '')) === '' && trim((string) ($link->iconclass ?? '')) !== ''
                        && trim((string) ($link->url ?? '')) !== '') {
                        $affected[] = htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8') . ' (ID ' . (int) $item->id . ')';
                        break;
                    }
                }
            }

            if ($affected) {
                Factory::getApplication()->enqueueMessage(
                    Text::sprintf('MOD_PRETTYLINKS_INSTALLERSCRIPT_ICON_ONLY_WARNING', implode(', ', $affected)),
                    'warning'
                );
            }
        } catch (\Throwable $e) {
            // The warning is informational only; never block the update because of it.
            Log::add($e->getMessage(), Log::WARNING, 'jerror');
        }
    }
};
