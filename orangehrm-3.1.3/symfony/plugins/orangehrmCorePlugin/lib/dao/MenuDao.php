<?php
/*
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
 * Menu Dao
 */
class MenuDao {
    
    public function getMenuItemList($userRoleList) {
        
        try {
                
            if (count($userRoleList) == 0) {
                return new Doctrine_Collection('MenuItem');
            }
            
            $roleNames = array();
            
            foreach($userRoleList as $role) {
                
                if ($role instanceof UserRole) {
                    $roleNames[] = $role->getName();
                } else if (is_string($role)) {
                    $roleNames[] = $role;
                }
                
            }            
            
            $query = Doctrine_Query::create()
                    ->from('MenuItem mi')
                    ->leftJoin('mi.Screen sc')
                    ->leftJoin('sc.Module mo')
                    ->leftJoin('sc.ScreenPermission sp')
                    ->leftJoin('sp.UserRole ur')
                    ->andWhere('mo.status = ?', Module::ENABLED)
                    ->andWhere('mi.status = ?', MenuItem::STATUS_ENABLED)
                    ->andWhere('sp.can_read = 1')
                    ->whereIn('ur.name', $roleNames)
                    ->orWhere('mi.screenId IS NULL')
                    ->orderBy('mi.orderHint ASC');

            return $query->execute();
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }    
        // @codeCoverageIgnoreEnd        
        
    }
    
    public function enableModuleMenuItems($moduleName, $menuTitles = array()) {
        
        try {
            
            $query = Doctrine_Query::create()
                    ->from('MenuItem mi')
                    ->leftJoin('mi.Screen sc')
                    ->leftJoin('sc.Module mo')
                    ->andWhere('mo.name = ?', $moduleName)
                    ->andWhere('mi.status = ?', MenuItem::STATUS_DISABLED);
            if (!empty($menuTitles)) {
                $query->andWhereIn('mi.menu_title', $menuTitles);
            }
            $menuItemList = $query->execute();
            $i = 0;
            
            foreach ($menuItemList as $menuItem) {
                
                $menuItem->setStatus(MenuItem::STATUS_ENABLED);
                $menuItem->save();
                $i++;
                
            }
            
            return $i;
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }    
        // @codeCoverageIgnoreEnd        
        
        
        
        
    }
    
}