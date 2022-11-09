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
 * Class MarketplaceService
 */
class MarketplaceService extends ConfigService
{
    private $marketplaceDao = null;
    private $sysConfig = null;
    /**
     * key name for ID in hs_hr_config table
     */
    const CLIENT_ID = "client_id";
    /**
     * key name for Scret in hs_hr_config table
     */
    const CLIENT_SECRET = "client_secret";
    /**
     * key name for marketpalce URL in hs_hr_config table
     */
    const BASE_URL = 'base_url';

    /**
     * @return array
     */
    public function getInstalledAddons()
    {
        return $this->getMarketplaceDao()->getInstalledAddons();
    }

    /**
     * @return MarketplaceDao
     */
    public function getMarketplaceDao()
    {
        if (!isset($this->marketplaceDao)) {
            $this->marketplaceDao = new MarketplaceDao();
        }
        return $this->marketplaceDao;
    }

    /**
     * @return String
     * @throws CoreServiceException
     */
    public function getClientId()
    {
        return $this->_getConfigValue(self::CLIENT_ID);
    }

    /**
     * @param $clientId
     * @throws CoreServiceException
     */
    public function setClientId($clientId)
    {
        $this->_setConfigValue(self::CLIENT_ID, $clientId);
    }

    /**
     * @return String
     * @throws CoreServiceException
     */
    public function getClientSecret()
    {
        return $this->_getConfigValue(self::CLIENT_SECRET);
    }

    /**
     * @param $clientSecret
     * @throws CoreServiceException
     */
    public function setClientSecret($clientSecret)
    {
        $this->_setConfigValue(self::CLIENT_SECRET, $clientSecret);
    }

    /**
     * @return String
     * @throws CoreServiceException
     */
    public function getBaseURL()
    {
        return $this->_getConfigValue(self::BASE_URL);
    }

    /**
     * @return array
     * @throws DaoException
     */
    public function getInstalationPendingAddonIds()
    {
        $pendingAddons = $this->getMarketplaceDao()->getInstalationPendingAddonIds();
        $addonlist = [];
        foreach ($pendingAddons as $addon) {
            $addonlist[] = $addon['id'];
        }
        return $addonlist;
    }

    /**
     * @return array $paidAddonIds
     * @throws DaoException
     */
    public function getPaidAddonIds()
    {
        $paidAddons = $this->getMarketplaceDao()->getAddonByStatus(MarketplaceDao::ADDON_STATUS_PAID);
        return $paidAddons;
    }

    /**
     * @return array $renewedAddonIds
     * @throws DaoException
     */
    public function getRenewedAddonIds()
    {
        return $this->getMarketplaceDao()->getAddonByStatus(MarketplaceDao::ADDON_STATUS_RENEWED);
    }

    /**
     * @return array $renewPendingAddonIds
     * @throws DaoException
     */
    public function getRenewPendingAddonIds()
    {
        return $this->getMarketplaceDao()->getAddonByStatus(MarketplaceDao::ADDON_STATUS_RENEW_REQUESTED);
    }

    /**
     * @param $data
     * @return bool
     * @throws DaoException
     */
    public function installOrRequestAddon($data)
    {
        return $this->getMarketplaceDao()->installOrRequestAddon($data);
    }

    /**
     * @param $data
     * @return bool
     * @throws DaoException
     */
    public function updateAddon($data)
    {
        return $this->getMarketplaceDao()->updateAddon($data);
    }

    /**
     * @param array $addonNames
     * @param string $fromStatus
     * @param string$toStatus
     * @return bool
     * @throws DaoException
     */
    public function changeAddonStatus($addonNames, $fromStatus, $toStatus)
    {
        return $this->getMarketplaceDao()->changeAddonStatus( $addonNames,$fromStatus, $toStatus);
    }

    /**
     * @param $addonId
     * @return Doctrine_Collection
     * @throws Exception
     */
    public function uninstallAddon($addonId)
    {
        return $this->getMarketplaceDao()->uninstallAddon($addonId);
    }

    /**
     * Extract an add-on zip file to the plugins directory
     * @param $addonFilePath
     * @return string
     * @throws Exception
     */
    public function extractAddonFile($addonFilePath)
    {
        try {
            if (class_exists(ZipArchive::class)) {
                $zip = new ZipArchive();
                if ($zip->open($addonFilePath) === true) {
                    $pluginName = $zip->getNameIndex(0);
                    if (is_writable(sfConfig::get('sf_plugins_dir'))) {
                        $zip->extractTo(sfConfig::get('sf_plugins_dir'));
                        $zip->close();
                        return str_replace('/', '', $pluginName);
                    }
                }
            }
        } catch (Exception $e) {
            throw new Exception('Plugin folder does not have write permissions.', 1000);
        }
    }

    /**
     * @param $addonId
     * @return Doctrine_Collection
     * @throws DaoException
     */
    public function getAddonById($addonId, $asArray = false)
    {
        return $this->getMarketplaceDao()->getAddonById($addonId, $asArray);
    }

    /**
     * Return instance of SystemConfiguration class
     * @return null|SystemConfiguration
     */
    public function getSysConfig()
    {
        require_once(sfConfig::get('sf_root_dir') . "/../installer/SystemConfiguration.php");
        if (is_null($this->sysConfig)) {
            $this->sysConfig = new SystemConfiguration();
        }
        return $this->sysConfig;
    }

    /**
     * Create instance identifier and checksum value for the instance identifier
     * @return array
     * @throws CoreServiceException
     * @throws DaoException
     */
    public function createInstanceIdentifierAndChecksum()
    {
        $organizationInfo = $this->getMarketplaceDao()->getOrganizationGeneralInformation();
        $organizationName = "OrganizationName";
        $country = "";

        if ($organizationInfo instanceof Organization) {
            $organizationName = $organizationInfo->getName();
            $country = $organizationInfo->getCountry();
        }

        $adminEmployee = $this->getMarketplaceDao()->getAdmin();
        $organizationEmail = "OrganizationEmail";
        $adminFirstName = "";
        $adminLastName = "";

        if ($adminEmployee instanceof Employee) {
            $organizationEmail = $adminEmployee->getEmpWorkEmail();
            $adminFirstName = $adminEmployee->getFirstName();
            $adminLastName = $adminEmployee->getLastName();
        }

        $currentTime = new DateTime();
        $currentTimestamp = $currentTime->getTimestamp();
        $instanceId = $this->getSysConfig()->createInstanceIdentifier(
            $organizationName,
            $organizationEmail,
            $adminFirstName,
            $adminLastName,
            $_SERVER['HTTP_HOST'],
            $country,
            $this->getSysConfig()->getOhrmVersion(),
            $currentTimestamp
        );
        $instanceIdChecksum = $this->getSysConfig()->createInstanceIdentifierChecksum(
            $organizationName,
            $organizationEmail,
            $adminFirstName,
            $adminLastName,
            $_SERVER['HTTP_HOST'],
            $country,
            $this->getSysConfig()->getOhrmVersion(),
            $currentTimestamp
        );
        $this->_setConfigValue(ConfigService::KEY_INSTANCE_IDENTIFIER, $instanceId);
        $this->_setConfigValue(ConfigService::KEY_INSTANCE_IDENTIFIER_CHECKSUM, $instanceIdChecksum);

        return array(
            'instanceId' => $this->_getConfigValue(ConfigService::KEY_INSTANCE_IDENTIFIER),
            'instanceIdChecksum' => $this->_getConfigValue(ConfigService::KEY_INSTANCE_IDENTIFIER_CHECKSUM)
        );
    }

    /**
     * @param $addon array object
     * @return array $notInstalledExtensions
     */
    public function addonPrerequisitesVerify($addon)
    {
        $notInstalledExtensions = [];
        if ($addon["type"] == "paid") {
            if (!extension_loaded("ionCube Loader")) {
                $notInstalledExtensions[] = 'ionCube Loader';
            }
        }
        if (count($addon["prerequisites"]) != 0) {
            foreach ($addon["prerequisites"] as $prerequisiteType) {
                if ($prerequisiteType["type"] == "php_extension") {
                    $php_extensions = explode(',', $prerequisiteType["params"]["extension"]);
                    foreach ($php_extensions as $php_extension) {
                        if (!extension_loaded($php_extension)) {
                            $notInstalledExtensions[] = $php_extension;
                        }
                    }
                }
            }
        }

        return $notInstalledExtensions;

    }

    /**
     * @return array expiryDates of the installed paid addons
     */
    public function getExpirationDatesOfInstalledPaidAddons()
    {
        $paidTypeInstalledAddons = $this->getMarketplaceDao()->getPaidTypeInstalledAddons();
        $expirationDates = [];
        foreach ($paidTypeInstalledAddons as $addon) {
            $addonInfo = require(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $addon['PluginName'] . DIRECTORY_SEPARATOR . 'addon_info.php');
            $expirationDates[$addon['id']] = date(sfContext::getInstance()->getUser()->getDateFormat(), $addonInfo['expiryTime']);
        }
        return $expirationDates;
    }


    /**
     * Check whether the installed paid type addons have expired and if expired
     * update the status of the addon as "Expired"
     */
    public function markExpiredAddons()
    {
        $paidTypeInstalledAddons = $this->getMarketplaceDao()->getPaidTypeInstalledAddons();
        $expiredAddonNames = [];
        if(count($paidTypeInstalledAddons)!=0) {
            foreach ($paidTypeInstalledAddons as $paidTypeInstalledAddon) {
                $addonInfo = require(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $paidTypeInstalledAddon['PluginName'] . DIRECTORY_SEPARATOR . 'addon_info.php');
                if ($addonInfo['expired']) {
                    $expiredAddonNames[] = $paidTypeInstalledAddon['addonName'];
                }
            }
        }

        if(count($expiredAddonNames) > 0) {
            $this->getMarketplaceDao()->changeAddonStatus(
                $expiredAddonNames,
                MarketplaceDao::ADDON_STATUS_INSTALLED,
                MarketplaceDao::ADDON_STATUS_EXPIRED
            );
        }
    }

    /**
     * @return array $expiredAddonIds
     * @throws DaoException
     */
    public function getExpiredAddons()
    {
        return $this->getMarketplaceDao()->getAddonByStatus(MarketplaceDao::ADDON_STATUS_EXPIRED);
    }
}
