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
    
    protected $systemUserDao = null;
    

    /**
     *
     * @return SystemUserDao
     */
    public function getSystemUserDao() {
        if (empty($this->systemUserDao)) {
            $this->systemUserDao = new SystemUserDao();
        }
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
    public function saveSystemUser(SystemUser $systemUser,$changePassword = false){
        
        try {
            
            if ($changePassword) {
                $systemUser->setUserPassword(md5($systemUser->getUserPassword()));
            }

            return $this->getSystemUserDao()->saveSystemUser($systemUser);
            
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(),$e->getCode(),$e);
        }
        
    }
    
    /**
     * Check is existing user according to user name
     * 
     * @param type $userName 
     * @param int $userId
     * @return mixed , false if user not exist  , otherwise it returns SystemUser object
     */
    public function isExistingSystemUser( $userName , $userId){
        try {
           return  $this->getSystemUserDao()->isExistingSystemUser( $userName , $userId);
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
     * Return an array of System User Ids
     * 
     * <pre>
     * 
     * The output will be an array like below.
     * 
     * array(
     *          0 => 1,
     *          1 => 2,
     *          2 => 3
     * )
     * </pre>
     * 
     * @version 2.7.1
     * @return Array of System User Ids
     */
    public function getSystemUserIdList(){
        return $this->getSystemUserDao()->getSystemUserIdList();
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
    public function getAssignableUserRoles(){
        try {
           return $this->getSystemUserDao()->getAssignableUserRoles();
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(),$e->getCode(),$e);
        }
    }
    
    /**
     * Get User role with given name
     * 
     * @param String $roleName Role Name
     * @return Doctrine_Collection UserRoles 
     */
    public function getUserRole($roleName){
        try {
           return $this->getSystemUserDao()->getUserRole($roleName);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(),$e->getCode(),$e);
        }
    }    
    
    public function getNonPredefinedUserRoles(){
        return$this->getSystemUserDao()->getNonPredefinedUserRoles();
    }    
    
   /**
     * Get Count of Search Query 
     * 
     * @param type $searchClues
     * @return int 
     */
    public function getSearchSystemUsersCount( $searchClues ){
        try {
           return $this->getSystemUserDao()->getSearchSystemUsersCount( $searchClues );
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(),$e->getCode(),$e);
        }
    }
    
    /**
     * Search System Users 
     * 
     * @param type $searchClues
     * @return type 
     */
     public function searchSystemUsers( $searchClues){
         try {
           return $this->getSystemUserDao()->searchSystemUsers( $searchClues );
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(),$e->getCode(),$e);
        }
     }
     
     public function isCurrentPassword($userId, $password) {
         
         $systemUser = $this->getSystemUserDao()->getSystemUser($userId);
         
         if (!($systemUser instanceof SystemUser)) {
             return false;
         }
         
         if ($systemUser->getUserPassword() == md5($password)) {
             return true;
         }
         
         return false;
         
     }
     
     /**
      * Updates the password of given user
      * 
      * @param int $userId User ID of the user
      * @param string $password Non-encrypted password
      * @return int 
      */     
     public function updatePassword($userId, $password) {
         return $this->getSystemUserDao()->updatePassword($userId, md5($password));
     }
    
}