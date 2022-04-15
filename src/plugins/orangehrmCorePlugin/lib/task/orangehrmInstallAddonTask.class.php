<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */

class orangehrmInstallAddonTask extends sfBaseTask
{
    private $addonName = null;

    protected function configure()
    {
        $this->addArguments(array(
            new sfCommandArgument('name', sfCommandArgument::OPTIONAL, 'The plugin/addon name'),
        ));

        $this->addOptions(array(
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
        ));

        $this->namespace = 'orangehrm';
        $this->name = 'install-addon';
        $this->briefDescription = 'Installs Given OrangeHRM Marketplace Addon';
        $this->detailedDescription = <<<EOF
The [orangehrm:install-addon|INFO] task installs a addon:

  [./symfony orangehrm:install-addon orangehrmAbcPlugin|INFO]
EOF;

    }

    protected function execute($arguments = array(), $options = array())
    {
        $pluginName = $arguments['name'];
        $optEnv = $options['env'];
        $optConnection = $options['connection'];

        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($optConnection)->getConnection();

        if (empty($pluginName)) {
            throw new sfCommandException('Plugin/addon name must be specified as an argument');
        }

        $this->addonName = $pluginName;

        if ($this->isAddonInstalled()) {
            throw new sfCommandException("Plugin/addon `$pluginName` already installed");
        }

        if ($this->isAddonRecordExist()) {
            if ($this->isAddonTypePaid() && !$this->hasPaidForAddon()) {
                throw new sfCommandException("Please paid for `$pluginName`");
            }
        }

        $cmd = "php symfony orangehrm:install-plugin --env=$optEnv --connection=$optConnection $pluginName";
        exec($cmd, $installPluginResponse, $installPluginStatus);
        foreach ($installPluginResponse as $log) {
            $this->logSection('orangehrm', $log);
        }
        if ($installPluginStatus !== 0) {
            throw new sfCommandException("orangehrm:install-plugin failed to install `$pluginName`");
        }

        try {
            if ($this->updateAddonStatusAsInstalled()) {
                $this->logSection('orangehrm', $pluginName . " installed");
                return;
            }
            throw new sfCommandException();
        } catch (Exception $e) {
            throw new sfCommandException("Plugin installed. But failed to update installed state.", 1, $e);
        }
    }

    private function isAddonInstalled()
    {
        $q = Doctrine_Query::create()
            ->select('id')
            ->from('Addon a')
            ->where('a.PluginName = ?', $this->addonName)
            ->andWhere('a.addonStatus = ?', MarketplaceDao::ADDON_STATUS_INSTALLED);
        $value = $q->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
        return !empty($value);
    }

    private function isAddonTypePaid()
    {
        $q = Doctrine_Query::create()
            ->select('id')
            ->from('Addon a')
            ->where('a.PluginName = ?', $this->addonName)
            ->andWhere('a.addonType = ?', MarketplaceDao::ADDON_TYPE_PAID);
        $value = $q->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
        return !empty($value);
    }

    private function hasPaidForAddon()
    {
        $q = Doctrine_Query::create()
            ->select('id')
            ->from('Addon a')
            ->where('a.PluginName = ?', $this->addonName)
            ->andWhere('a.addonStatus = ?', MarketplaceDao::ADDON_STATUS_PAID);
        $value = $q->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
        return !empty($value);
    }

    private function isAddonRecordExist()
    {
        $q = Doctrine_Query::create()
            ->select('id')
            ->from('Addon a')
            ->where('a.PluginName = ?', $this->addonName);
        $value = $q->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
        return !empty($value);
    }

    private function updateAddonStatusAsInstalled()
    {
        $success = Doctrine::getTable('Addon')
            ->createQuery('a')
            ->update()
            ->set('a.addonStatus', '?', MarketplaceDao::ADDON_STATUS_INSTALLED)
            ->set('a.installedDate', '?', date('Y-m-d H:i:s'))
            ->where('a.PluginName = ?', $this->addonName)
            ->execute();

        return $success;
    }
}
