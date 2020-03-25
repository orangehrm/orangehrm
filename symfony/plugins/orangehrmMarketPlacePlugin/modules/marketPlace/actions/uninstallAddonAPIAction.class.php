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
 * Boston, MA 02110-1301, USA
 */

/**
 * Class uninstallAddonAPIAction
 */
class uninstallAddonAPIAction extends baseAddonAction
{
    /**
     * @param $request
     * @return string
     */
    public function execute($request)
    {
        try {
            if (ini_get('max_execution_time') < 600) {
                ini_set('max_execution_time', 600);
            }
            $data = $request->getParameterHolder()->getAll();
            $result = $this->uninstallAddon($data['uninstallAddonID']);
            echo json_encode($result);
            return sfView::NONE;
        } catch (Exception $e) {
            $this->getMarketPlaceLogger()->error($e->getCode() . ' : ' . $e->getMessage());
            $this->getMarketPlaceLogger()->error($e->getTraceAsString());
            echo json_encode($e->getCode());
            return sfView::NONE;
        }
    }

    /**
     * @param $addonid
     * @return Doctrine_Collection
     * @throws DaoException
     * @throws Doctrine_Transaction_Exception
     */
    public function uninstallAddon($addonid)
    {
        $addonDetail = $this->getMarcketplaceService()->getAddonById($addonid);
        if ($addonDetail instanceof Addon) {
            $pluginName = $addonDetail->getPluginName();
        } else {
            throw new Exception('Selected plugin to uninstall is not tracked in database.', 2000);
        }
        $symfonyPath = sfConfig::get('sf_root_dir');
        $pluginInstallFilePath = $symfonyPath . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'plugin_uninstall.php';
        try {
            $connection = Doctrine_Manager::getInstance()->getCurrentConnection();
            $connection->beginTransaction();
            $uninstall = require_once($pluginInstallFilePath);
            if (!$uninstall) {
                throw new Exception('Uninstall file execution fails.', 2001);
            }
            $deletingPlugin = $this->recursiveDeletePlugin($symfonyPath . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $pluginName);
            if (!$deletingPlugin) {
                throw new Exception('Removing plugin folder fails.', 2002);
            }
            chdir($symfonyPath);
            exec("php symfony cc", $symfonyCcResponse, $symfonyCcStatus);
            if ($symfonyCcStatus != 0) {
                throw new Exception('Running php symfony cc fails.', 2003);
            }
            chdir($symfonyPath);
            exec("php symfony o:publish-asset", $publishAssetResponse, $publishAssetStatus);
            if ($publishAssetStatus != 0) {
                throw new Exception('Running php symfony o:publish-asset fails.', 2004);
            }
            chdir($symfonyPath);
            exec("php symfony d:build-model", $buildModelResponse, $buildModelStatus);
            if ($buildModelStatus != 0) {
                throw new Exception('Running php symfony d:build-model fails.', 2005);
            }
            $result = $this->getMarcketplaceService()->uninstallAddon($addonid);
            $connection->commit();

            // clearing menu item cache so that new menus will be added.
            $this->getUser()->getAttributeHolder()->remove(mainMenuComponent::MAIN_MENU_USER_ATTRIBUTE);
            return $result;
        } catch (Exception $e) {
            $connection->rollback();
            throw $e;
        }
    }

    /**
     * @param $directory
     * @return bool
     */
    public function recursiveDeletePlugin($directory)
    {
        $dir = opendir($directory);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                $full = $directory . DIRECTORY_SEPARATOR . $file;
                if (is_dir($full)) {
                    $this->recursiveDeletePlugin($full);
                } else {
                    unlink($full);
                }
            }
        }
        closedir($dir);
        return rmdir($directory);
    }
}
