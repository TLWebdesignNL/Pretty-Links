<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_prettylinks
 *
 * @copyright   Copyright (C) 2022 TLWebdesign. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\Database\DatabaseInterface;
use Joomla\Registry\Registry;

/**
 * Script file of Prettylinks module
 */
class mod_prettylinksInstallerScript
{
    /**
     * Extension script constructor.
     *
     * @return  void
     */
    public function __construct()
    {
        $this->minimumJoomla = '4.0';
        $this->minimumPhp    = JOOMLA_MINIMUM_PHP;
    }

    /**
     * Method to install the extension
     *
     * @param   InstallerAdapter  $parent  The class calling this method
     *
     * @return  boolean  True on success
     */
    function install($parent)
    {
        echo Text::_('MOD_PRETTYLINKS_INSTALLERSCRIPT_INSTALL');

        return true;
    }

    /**
     * Method to uninstall the extension
     *
     * @param   InstallerAdapter  $parent  The class calling this method
     *
     * @return  boolean  True on success
     */
    function uninstall($parent)
    {
        echo Text::_('MOD_PRETTYLINKS_INSTALLERSCRIPT_UNINSTALL');

        return true;
    }

    /**
     * Method to update the extension
     *
     * @param   InstallerAdapter  $parent  The class calling this method
     *
     * @return  boolean  True on success
     */
    function update($parent)
    {
        echo Text::_('MOD_PRETTYLINKS_INSTALLERSCRIPT_UPDATE');

        return true;
    }

    /**
     * Function called before extension installation/update/removal procedure commences
     *
     * @param   string            $type    The type of change (install, update or discover_install, not uninstall)
     * @param   InstallerAdapter  $parent  The class calling this method
     *
     * @return  boolean  True on success
     */
    function preflight($type, $parent)
    {
        // Check for the minimum PHP version before continuing
        if (!empty($this->minimumPhp) && version_compare(PHP_VERSION, $this->minimumPhp, '<')) {
            Log::add(Text::sprintf('JLIB_INSTALLER_MINIMUM_PHP', $this->minimumPhp), Log::WARNING, 'jerror');

            return false;
        }

        // Check for the minimum Joomla version before continuing
        if (!empty($this->minimumJoomla) && version_compare(JVERSION, $this->minimumJoomla, '<')) {
            Log::add(Text::sprintf('JLIB_INSTALLER_MINIMUM_JOOMLA', $this->minimumJoomla), Log::WARNING, 'jerror');

            return false;
        }

        echo Text::_('MOD_PRETTYLINKS_INSTALLERSCRIPT_PREFLIGHT');

        return true;
    }

    /**
     * Function called after extension installation/update/removal procedure commences
     *
     * @param   string            $type    The type of change (install, update or discover_install, not uninstall)
     * @param   InstallerAdapter  $parent  The class calling this method
     *
     * @return  boolean  True on success
     */
    function postflight($type, $parent)
    {
        echo Text::_('MOD_PRETTYLINKS_INSTALLERSCRIPT_POSTFLIGHT');

        if ($type === 'update') {
            $this->warnAboutIconOnlyLinks();
        }

        return true;
    }

    /**
     * Warn about modules with icon-only links: since 1.1.0 a link needs text for its accessible name,
     * so these rows are rendered as an icon without a link until text is added.
     *
     * @return  void
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
}
