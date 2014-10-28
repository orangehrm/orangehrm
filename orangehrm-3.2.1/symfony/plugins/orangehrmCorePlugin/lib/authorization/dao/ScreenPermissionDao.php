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
 */

/**
 * Screen Permission Dao
 */
class ScreenPermissionDao {
   
    /**
     *
     * @param string $module Module Name
     * @param string $actionUrl Action
     * @param array $roles Array of UserRole objects or user role names
     */
    public function getScreenPermissions($module, $actionUrl, $roles) {
        try {
            $roleNames = array();
            
            foreach($roles as $role) {
                if ($role instanceof UserRole) {
                    $roleNames[] = $role->getName();
                } else if (is_string($role)) {
                    $roleNames[] = $role;
                }
            }
            
            $query = Doctrine_Query::create()
                    ->from('ScreenPermission sp')
                    ->leftJoin('sp.UserRole ur')
                    ->leftJoin('sp.Screen s')
                    ->leftJoin('s.Module m')
                    ->where('m.name = ?', $module)
                    ->andWhere('s.action_url = ?', $actionUrl)
                    ->andWhereIn('ur.name', $roleNames);

            return $query->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }        
    }
}

