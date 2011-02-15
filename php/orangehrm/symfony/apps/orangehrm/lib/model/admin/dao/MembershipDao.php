<?php
/**
 * Membership DAO class for CRUD operations
 *
 * @author Sujith T
 */
class MembershipDao extends BaseDao {

   /**
    * Retrieve MembershipTypeList
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws DaoException
    */
   public function getMembershipTypeList($orderField = 'membtype_code', $orderBy = 'ASC') {
      try {
         $q = Doctrine_Query::create()
            ->from('MembershipType')
            ->orderBy($orderField.' '.$orderBy);

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Saves MembershipType
    * @param MembershipType $membershipType
    * @returns boolean
    * @throws DaoException, DataDuplicationException
    */
   public function saveMembershipType(MembershipType $membershipType) {
      try {
         $q = Doctrine_Query::create()
             ->from('MembershipType m')
             ->where('m.membtype_name = ?', $membershipType->membtype_name);

         if (!empty($membershipType->membtype_code)) {
            $q->andWhere('m.membtype_code <> ?', $membershipType->membtype_code) ;
         }

         if ($q->count() > 0) {
            throw new DataDuplicationException("Failed saving Membership Type due to duplicated record");
         }

         if($membershipType->getMembtypeCode() == '') {
            $idGenService	=	new IDGeneratorService();
            $idGenService->setEntity($membershipType);
            $membershipType->setMembtypeCode( $idGenService->getNextID());
         }

         $membershipType->save();
         return true;
      } catch(Doctrine_Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete MembershipType
    * @param array() $membershipType
    * @returns boolean
    * @throws DaoException
    */
   public function deleteMembershipType($membershipType = array()) {
      try {
         if(is_array($membershipType)) {
            $q = Doctrine_Query::create()
               ->delete('MembershipType')
               ->whereIn('membtype_code', $membershipType);

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
    * Search MembershipType
    * @param String $searchMode
    * @param String $searchValue
    * @returns Collection
    * @throws DaoException
    */
   public function searchMembershipType($searchMode, $searchValue) {
      try {
         $q = 	Doctrine_Query::create( )
            ->from('MembershipType')
            ->where("$searchMode = ?", trim($searchValue));

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Read MembershipType by Id. Should refactor to return Model Object
    * @param String $id
    * @returns MembershipType
    * @throws DaoException
    */
   public function readMembershipType($id) {
      try {
         return Doctrine::getTable('MembershipType')->find($id);
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Retrieve Membership list
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws DaoException
    */
   public function getMembershipList($orderField='membship_code', $orderBy='ASC') {
      try {
         $q = Doctrine_Query::create()
             ->from('Membership')
             ->orderBy($orderField.' '.$orderBy);

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Save Membership
    * @param Membership $membership
    * @returns boolean
    * @throws DaoException
    */
   public function saveMembership(Membership $membership) {
      try {
         $q = Doctrine_Query::create()
             ->from('Membership m')
             ->where('m.membship_name = ?', $membership->membship_name)
             ->andWhere('m.membtype_code = ?', $membership->membtype_code);

         if (!empty($membership->membship_code)) {
            $q->andWhere('m.membship_code <> ?', $membership->membship_code) ;
         }

         if ($q->count() > 0) {
             throw new DataDuplicationException("Can't save Mem,bership due to data duplication");
         }

         if( $membership->getMembshipCode() == '') {
            $idGenService	=	new IDGeneratorService();
            $idGenService->setEntity($membership);
            $membership->setMembshipCode($idGenService->getNextID());
         }

         $membership->save();
         return true;
      } catch(Doctrine_Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete Membership
    * @param array() $membershipList
    * @returns boolean
    * @throws DaoException
    */
   public function deleteMembership($membershipList = array()) {
      try {
         if(is_array($membershipList)) {
            $q = Doctrine_Query::create()
               ->delete('Membership')
               ->whereIn('membship_code',  $membershipList );

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
    * Search Membership
    * @param String $searchMode
    * @param String $searchValue
    * @returns Collection
    * @throws DaoException
    */
   public function searchMembership($searchMode, $searchValue) {
      try {
         $q = 	Doctrine_Query::create()
            ->from('Membership')
            ->where("$searchMode = ?",trim($searchValue));

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Return Membership by its Id. need to refactor to return model object
    * @param String $id
    * @returns Membership
    * @throws DaoException
    */
   public function readMembership($id) {
      try {
         return Doctrine::getTable('Membership')->find($id);
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }
}
?>
