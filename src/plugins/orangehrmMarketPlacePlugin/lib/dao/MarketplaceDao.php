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
 * Class MarketplaceDao
 */
class MarketplaceDao
{
    const ADDON_STATUS_INSTALLED = 'Installed';
    const ADDON_STATUS_REQUESTED = 'Requested';
    const ADDON_STATUS_PAID = 'Paid';
    const ADDON_STATUS_EXPIRED = 'Expired';
    const ADDON_STATUS_RENEW_REQUESTED = 'Renew requested';
    const ADDON_STATUS_RENEWED = 'Renewed';

    const ADDON_TYPE_PAID = 'paid';
    const ADDON_TYPE_FREE = 'free';
    /**
     * @return array
     * @throws DaoException
     */
    public function getInstalledAddons()
    {
        try {
            $q = Doctrine_Query::create()
                ->from('Addon c')
                ->where('c.status = ?', self::ADDON_STATUS_INSTALLED);
            return $q->execute(array(), Doctrine::HYDRATE_ARRAY);
        } catch (Exception $e) {
            $this->getLogger()->error("Exception in getValue:" . $e);
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return array
     * @throws DaoException
     */
    public function getInstalationPendingAddonIds()
    {
        try {
            $q = Doctrine_Query::create()
                ->select('id')
                ->from('Addon c')
                ->where('c.status = ?', self::ADDON_STATUS_REQUESTED);
            $value = $q->execute(array(), Doctrine::HYDRATE_ARRAY);
            return $value;
        } catch (Exception $e) {
            $this->getLogger()->error("Exception in getValue:" . $e);
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
    /**
     * @param string $addonStatus
     * @return array $addonIds
     * @throws DaoException
     */
    public function getAddonByStatus($status) {
        try {
            $q = Doctrine_Query::create()
                ->select('id')
                ->from('Addon c')
                ->where('c.status = ?', $status);
            $value = $q->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
            if(!is_array($value)){
                $value = array($value);
            }
            return $value;
        } catch (Exception $e) {
            $this->getLogger()->error("Exception in getValue:" . $e);
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
    /**
     * @param $data
     * @return bool
     * @throws DaoException
     */
    public function installOrRequestAddon($data)
    {
        try {
            $addon = new Addon();
            $addon->setId($data['id']);
            $addon->setAddonName($data['addonName']);
            $addon->setInstalledDate(date('Y-m-d H:i:s'));
            $addon->setAddonType($data['type']);
            $addon->setAddonStatus($data['status']);
            if ($data['pluginName']) {
                $addon->setPluginName($data['pluginName']);
            }
            $addon->save();
            return true;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param $data
     * @return bool
     * @throws DaoException
     */
    public function updateAddon($data)
    {
        try {
            $addon = Doctrine::getTable('Addon')->find($data['id']);
            $addon->setInstalledDate(date('Y-m-d H:i:s'));
            $addon->setAddonStatus($data['status']);
            $addon->setPluginName($data['pluginName']);
            if (isset($data['version'])) {
                $addon->setVersion($data['version']);
            }
            $addon->save();
            return true;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param array $addonNames
     * @param string $fromStatus
     * @param string$toStatus
     * @return int
     * @throws DaoException
     */
    public function changeAddonStatus($addonNames, $fromStatus, $toStatus)
    {
        try {
            if (!empty($addonNames)) {
                $q = Doctrine_Query::create()
                    ->update('Addon a')
                    ->set('a.addonStatus', '?', $toStatus)
                    ->whereIn('a.addonName', $addonNames)
                    ->andWhere('a.addonStatus = ?', $fromStatus);
                return $q->execute();
            }
            return 0;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param $addonId
     * @return bool
     * @throws DaoException
     */
    public function uninstallAddon($addonId)
    {
        try {
            $q = Doctrine_Query::create()
                ->delete('Addon l')
                ->where("l.id = ?", $addonId);
            $numDeleted = $q->execute();
            if ($numDeleted > 0) {
                return true;
            }
            return false;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param $addonId
     * @return Doctrine_Collection
     * @throws DaoException
     */
    public function getAddonById($addonId, $asArray = false)
    {
        try {
            $q = Doctrine_Query::create()
                ->select('*')
                ->from('Addon c')
                ->where('c.id = ?', $addonId);
            if ($asArray) {
                $q->setHydrationMode(Doctrine_Core::HYDRATE_ARRAY);
            }
            return $q->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return mixed
     * @throws DaoException
     */
    public function getOrganizationGeneralInformation()
    {
        try {
            return Doctrine::getTable('Organization')->find(1);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $userRole
     * @return mixed
     * @throws DaoException
     */
    public function getUserRoleId($userRole)
    {
        try {
            $q = Doctrine_Query::create()
                ->select('u.id')
                ->from('UserRole u')
                ->where('u.name = ?', $userRole);
            return $q->execute(array(), Doctrine::HYDRATE_ARRAY)[0]['id'];
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return mixed
     * @throws DaoException
     */
    public function getAdmin()
    {
        try {
            $adminRoleId = $this->getUserRoleId('Admin');
            $q = Doctrine_Query::create()
                ->select('u.emp_number')
                ->from('SystemUser u')
                ->where('u.user_role_id = ?', $adminRoleId);
            $empNumber = $q->execute(array(), Doctrine::HYDRATE_ARRAY)[0]['emp_number'];

            return Doctrine::getTable('Employee')->find($empNumber);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return array $paidTypeInstalledAddonIds
     * @throws DaoException
     */
    public function getPaidTypeInstalledAddons()
    {
        try {
            $q = Doctrine_Query::create()
                ->from('Addon c')
                ->where('c.addonStatus = ?', self::ADDON_STATUS_INSTALLED)
                ->andWhere('c.addonType = ?', self::ADDON_TYPE_PAID);
            $value = $q->execute(array(), Doctrine::HYDRATE_ARRAY);
            return $value;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

}
