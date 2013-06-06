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

/**
 * Description of HomePageDao
 */
class HomePageDao extends BaseDao {
    
    /**
     * Get home page records for the given user role ids in priority order. (Descending order of the priority field).
     * If two records have the same priority, the higher ID will be returned first. (Assuming the later entry was 
     * intended to override the earlier entry).
     * 
     * @param Array $userRoleIds Array of user role ids
     * @return Doctrine_Collection List of matching home page entries
     * 
     * @throws DaoException on an error from the database layer
     */
    public function getHomePagesInPriorityOrder($userRoleIds) {
        try {
            if (empty($userRoleIds)) {
                return new Doctrine_Collection('HomePage');
            } else {
                $query = Doctrine_Query::create()
                        ->from('HomePage h')
                        ->whereIn('h.user_role_id', $userRoleIds)
                        ->orderBy('h.priority DESC, h.id DESC');

                return $query->execute();
            }
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }        
    }
    
    /**
     * Get module default page records for the given module and given user role ids in priority order. 
     * (Descending order of the priority field).
     * If two records have the same priority, the higher ID will be returned first. (Assuming the later entry was 
     * intended to override the earlier entry).
     * 
     * @param Array $userRoleIds Array of user role ids
     * @param String $moduleName Module Name
     * @return Doctrine_Collection List of matching default page entries
     * 
     * @throws DaoException on an error from the database layer
     */
    public function getModuleDefaultPagesInPriorityOrder($moduleName, $userRoleIds) {
        try {
            if (empty($userRoleIds)) {
                return new Doctrine_Collection('ModuleDefaultPage');
            } else {
                $query = Doctrine_Query::create()
                        ->from('ModuleDefaultPage p')
                        ->leftJoin('p.Module m')
                        ->whereIn('p.user_role_id', $userRoleIds)
                        ->andWhere('m.name = ?', $moduleName)
                        ->orderBy('p.priority DESC, p.id DESC');

                return $query->execute();
            }
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }         
    }    
}
