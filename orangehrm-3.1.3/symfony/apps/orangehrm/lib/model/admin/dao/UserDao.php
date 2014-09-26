<?php
/**
 * UserDao for CRUD operation
 *
 */
class UserDao extends BaseDao {
   
   /**
    * Return UserGroup List
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws DaoException
    */
   public function getUserGroupList($orderField = 'userg_id', $orderBy = 'ASC') {
      try {
          $orderBy = strcasecmp($orderBy, 'DESC') === 0 ? 'DESC' : 'ASC';
         $q = Doctrine_Query::create()
             ->from('UserGroup')
             ->orderBy($orderField.' '.$orderBy);

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Saves User Group
    * @param String $orderField
    * @param String $orderBy
    * @returns UserGroup
    * @throws DaoException, DataDuplicationException
    */
   public function saveUserGroup(UserGroup $userGroup) {
      try {
         $q = Doctrine_Query::create()
            ->from('UserGroup u')
            ->where('u.userg_name = ?', $userGroup->userg_name);

         if (!empty($userGroup->userg_id)) {
            $q->andWhere('u.userg_id <> ?', $userGroup->userg_id) ;
         }
         $count = $q->count();
         if ($q->count() > 0) {
          throw new DataDuplicationException("Can't save UserGroup, it already exists");
         }

         if( $userGroup->getUsergId() == '') {
            $idGenService	=	new IDGeneratorService();
            $idGenService->setEntity($userGroup);
            $userGroup->setUsergId( $idGenService->getNextID());
         }
         $userGroup->save();
         return $userGroup ;
      } catch(Doctrine_Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete UserGroup
    * @param array() $userGroupList
    * @returns boolean
    * @throws DaoException
    */
   public function deleteUserGroup($userGroupList = array()) {
      try {
         if(is_array($userGroupList)) {
            $q = Doctrine_Query::create()
                   ->delete('UserGroup')
                   ->whereIn('userg_id', $userGroupList);

            $numDeleted = $q->execute();
            if($numDeleted > 0) {
               return true;
            }
            return false;
         }
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Search UserGroup
    * @param String $searchMode
    * @param String $searchValue
    * @returns Collection
    * @throws DaoException
    */
   public function searchUserGroup($searchMode, $searchValue) {
      try {
         $q = 	Doctrine_Query::create()
            ->from('UserGroup')
            ->where("$searchMode = ?", trim($searchValue));

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve UserGroup by Id
    * @param String $id
    * @returns UserGroup
    * @throws DaoException
    */
   public function readUserGroup($id) {
      try {
         return Doctrine::getTable('UserGroup')->find($id);
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve Users List
    * @param String $isAdmin
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws DaoException
    */
   public function getUsersList($isAdmin='Yes', $orderField='id', $orderBy='ASC') {
      try {
          $orderBy = strcasecmp($orderBy, 'DESC') === 0 ? 'DESC' : 'ASC';
         $q = Doctrine_Query::create()
             ->from('Users u')
             ->where('u.is_admin = ?', $isAdmin)
             ->orderBy($orderField.' '.$orderBy);

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Saves User
    * @param Users $user
    * @returns boolean
    * @throws DaoException
    */
   public function saveUser(Users $user) {
      try {
         if($user->getId() == '') {
            $idGenService	=	new IDGeneratorService();
            $idGenService->setEntity($user);
            $user->setId($idGenService->getNextID());
         }
         $user->save();
         return true;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Checks for the existence of user
    * @param String $userName
    * @returns boolean
    * @throws DaoException
    */
   public function isExistingUser($userName) {
      try {
         $q = Doctrine_Query::create()
            ->from('Users u')
            ->where("u.user_name = ?", $userName);

         return ($q->count()>0)?true:false;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Deletes Users
    * @param array() $userList
    * @returns boolean
    * @throws DaoException
    */
   public function deleteUser($userList = array()) {
      try {
         if(is_array($userList)) {
            $q = Doctrine_Query::create()
               ->delete('Users')
               ->whereIn('id', $userList);

            $numDeleted = $q->execute();
            if($numDeleted > 0) {
               return true;
            }
            return false;
         }
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Search Users
    * @param int $isAdmin
    * @param String $searchMode
    * @param String  $searchValue
    * @returns boolean
    * @throws DaoException
    */
   public function searchUsers($isAdmin=1, $searchMode, $searchValue) {
      try {
         $q = 	Doctrine_Query::create()
            ->from('Users u')
            ->where($searchMode . " LIKE ? ", "%" . trim($searchValue) . "%")
            ->andwhere("u.is_admin = ?", $isAdmin);

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve User based on their Id. This need to be refactored to return model object
    * @param int $id
    * @returns User
    * @throws DaoException
    */
   public function readUser($id) {
      try {
         return Doctrine::getTable('Users')->find($id);
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve Module List
    * @param UserGroup $userGrop
    * @returns Collection
    * @throws DaoException
    */
   public function getModuleList(UserGroup $userGrop) {
      try {
         $existingModule   =	array();
         $existingModules  =	$this->getUserGroupModelRights($userGrop);
         foreach($existingModules as $right) {
            array_push($existingModule,"'" . $right->getModule()->getModId(). "'");
         }

         $q = Doctrine_Query::create()
            ->from('Module m');

         if (count($existingModules) > 0) {
            $q->where("m.mod_id NOT IN (".implode(',',$existingModule).")");
         }
         $q->orderBy('mod_id ASC');
         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve User Group Model Rights
    * @param UserGroup $userGrop
    * @returns Collection
    * @throws DaoException
    */
   public function getUserGroupModelRights(UserGroup $userGrop) {
      try {
         $q = Doctrine_Query::create()
            ->from('ModuleRights')
            ->where("userg_id = ?", $userGrop->getUsergId());

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Saves ModuleRights
    * @param ModuleRights $moduleRights
    * @returns boolean
    * @throws DaoException
    */
   public function saveUserGroupModelRights(ModuleRights $moduleRights) {
      try {
         $moduleRights->save();
         return true;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete UserGroup ModelRights
    * @param UserGroup $userGrop
    * @returns boolean
    * @throws DaoException
    */
   public function deleteUserGroupModelRights(UserGroup $userGrop) {
      try {
         $q = Doctrine_Query::create()
             ->delete('ModuleRights')
             ->where("userg_id = ?", $userGrop->getUsergId());

         $numDeleted = $q->execute();
         if($numDeleted > 0) {
            return true;
         }
         return false;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }
}
?>