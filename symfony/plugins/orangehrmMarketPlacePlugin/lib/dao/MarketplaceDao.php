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
    /**
     * @return array
     * @throws DaoException
     */
    public function getInstalledAddonIds()
    {
        try {
            $q = Doctrine_Query::create()
                ->select('id')
                ->from('Addon c')
                ->where('c.status = ?', 'Installed');
            $value = $q->execute(array(), Doctrine::HYDRATE_ARRAY);
            return $value;
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
                ->where('c.status = ?', 'Requested');
            $value = $q->execute(array(), Doctrine::HYDRATE_ARRAY);
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
            $addon->setAddonStatus($data['status']);
            if ($data['pluginName']) {
                $addon->setPluginname($data['pluginName']);
            }
            $addon->save();
            return true;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function uninstallAddon($addonId)
    {
        try {
            $q = Doctrine_Query::create()
                ->delete('Addon l')
                ->where("l.id = ?", $addonId);
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
}
