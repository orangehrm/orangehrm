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
class SystemUserService extends BaseService{
    
    private $systemUserDao = null;
    
    /**
     * Constructor of System User Service class
     * 
     * Set System User Dao if object not intilaized
     */
    function __construct() {
        if( empty($this->systemUserDao)){
            $this->setSystemUserDao(new SystemUserDao());
        }
        
    }

    public function getSystemUserDao() {
        return $this->systemUserDao;
    }

    public function setSystemUserDao($systemUserDao) {
        $this->systemUserDao = $systemUserDao;
    }

    /**
     * Save System User
     * 
     * @param SystemUser $systemUser 
     * @return void
     */
    public function saveSystemUser( SystemUser $systemUser){
        try {
            $this->getSystemUserDao()->saveSystemUser( $systemUser );
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(),$e->getCode(),$e);
        }
    }
    
    /**
     * Check is existing user according to user name
     * 
     * @param type $userName 
     * @return mixed , false if user not exist  , otherwise it returns SystemUser object
     */
    public function isExistingSystemUser( $userName){
        try {
            $this->getSystemUserDao()->isExistingSystemUser( $userName);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(),$e->getCode(),$e);
        }
    }
    
    /**
     * Get System User for given User Id
     * 
     * @param type $userId
     * @return SystemUser  
     */
    public function getSystemUser( $userId ){
        try {
            return $this->getSystemUserDao()->getSystemUser( $userId );
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(),$e->getCode(),$e);
        }
    }
    
    /**
     * Get System Users
     * 
     * @return Doctrine_Collection 
     */
    public function getSystemUsers(){
        try {
            return $this->getSystemUserDao()->getSystemUsers();
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(),$e->getCode(),$e);
        }
    }
    
   /**
     * Delete System Users
     * @param array $deletedIds 
     * 
     */
    public function deleteSystemUsers( array $deletedIds){
        try {
            $this->getSystemUserDao()->deleteSystemUsers($deletedIds);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(),$e->getCode(),$e);
        }
    }
    
    /**
     * Get Pre Defined User Roles
     * 
     * @return Doctrine_Collection UserRoles 
     */
    public function getPreDefinedUserRoles(){
        try {
           return $this->getSystemUserDao()->getPreDefinedUserRole();
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(),$e->getCode(),$e);
        }
    }
    
}

?>
