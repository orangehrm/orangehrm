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
class SystemUserDao extends BaseDao{
    
    /**
     * Save System User
     * 
     * @param SystemUser $systemUser 
     * @return void
     */
    public function saveSystemUser( SystemUser $systemUser){
        try {
            $systemUser->save(); 
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(),$e->getCode(),$e);
        }
    }
    
    /**
     * Check is existing user according to user name
     * 
     * @param type $userName 
     * @return mixed , false if user not exist  , otherwise it returns 
     */
    public function isExistingSystemUser( $userName){
        try {
            $query = Doctrine_Query:: create()->from('SystemUser u')
                            ->where('u.user_name = ?', $userName);
            return $query->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(),$e->getCode(),$e);
        }
    }
    
    /**
     * Get System User 
     * 
     * @param type $userId
     * @return SystemUser  
     */
    public function getSystemUser( $userId ){
        try {
            return Doctrine :: getTable('SystemUser')->find($userId);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(),$e->getCode(),$e);
        }
    }
    
    /**
     * Get System Users
     * 
     * @return Doctrine_Collection 
     */
    public function getSystemUsers( ){
        try {
            $query = Doctrine_Query:: create()->from('SystemUser u');
                            
            return $query->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(),$e->getCode(),$e);
        }
    }
    
    public function searchSystemUsers( array $searchClues){
        
    }
    
    /**
     * Delete System Users
     * @param array $deletedIds 
     * 
     */
    public function deleteSystemUsers( array $deletedIds){
        try {
                $query = Doctrine_Query :: create()->delete('SystemUser u')
                                ->whereIn('u.id', $deletedIds);
                $query->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(),$e->getCode(),$e);
        }
    }
}

?>
