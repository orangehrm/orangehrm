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
 * Description of DataGroupDao
 *
 */
class DataGroupDao {
    
    public function getDataGroup($name) {
        try {
            $query = Doctrine_Query::create()
                    ->from('DataGroup d')
                    ->where('d.name = ?', $name);
            return $query->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }        
    }
    
    
    /*
     * Get non pre defined UserRoles
     * 
     * @return Array Array of UserRole objects
     */

    public function getDataGroupPermission($dataGroupName, $userRoleId, $selfPermission = false) {
        
       if(!is_array($dataGroupName) && $dataGroupName != null){
           $dataGroupName = array($dataGroupName);
       }
       
        try {
            $query = Doctrine_Query::create()
                    ->from('DataGroupPermission as p')
                    ->leftJoin('p.DataGroup as g')
                    ->andWhere('p.user_role_id = ?', $userRoleId);
                    if($dataGroupName != null){
                        $query->andWhereIn('g.name ', $dataGroupName);
                    }
                    if($selfPermission){
                        $query->andWhere('p.self = 1');
                    }else {
                        $query->andWhere('p.self = 0');
                    }

            return $query->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    
    /**
     *
     * @return Doctrine_Collection 
     */
    public function getDataGroups(){
         try {
            $query = Doctrine_Query::create()
                    ->from('DataGroup as g')
                    ->orderBy('g.description'); 
            return $query->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

}

