<?php
/**
 * NationalityDao for CRUD operation
 *
 * @author Sujith T
 */
class NationalityDao extends BaseDao {

   /**
    * Retrieve Nationality List
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws DaoException
    */
   public function getNationalityList($orderField = 'nat_code', $orderBy = 'ASC') {
      try {
         $q = Doctrine_Query::create()
             ->from('Nationality')
             ->orderBy($orderField.' '.$orderBy);

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Save Nationality
    * @param Nationality $nationality
    * @returns boolean
    * @throws DaoException
    */
   public function saveNationality(Nationality $nationality) {
      try {
         if( $nationality->getNatCode() == '') {
            $idGenService	=	new IDGeneratorService();
            $idGenService->setEntity($nationality);
            $nationality->setNatCode( $idGenService->getNextID() );
         }
         $nationality->save();
         return true;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete Nationality
    * @param array() $nationalityList
    * @returns boolean
    * @throws DaoException
    */
   public function deleteNationality($nationalityList = array()) {
      try {
         if(is_array($nationalityList )) {
            $q = Doctrine_Query::create()
               ->delete('Nationality')
               ->whereIn('nat_code', $nationalityList);

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
    * Search Nationality
    * @param String $searchMode
    * @param String $searchValue
    * @return Collection
    * @throws DaoException
    */
   public function searchNationality($searchMode, $searchValue) {
      try {
         $searchValue	=	trim($searchValue);
         $q = 	Doctrine_Query::create( )
               ->from('Nationality')
               ->where("$searchMode = ?",$searchValue);

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Read Nationality By Id. Need to refactor to return entity object
    * @param String $id
    * @return Nationality
    * @throws DaoException
    */
   public function readNationality($id) {
      try {
         return Doctrine::getTable('Nationality')->find($id);
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Return EthnicRace List
    * @param String $orderField
    * @param String $orderBy
    * @return Collection
    * @throws DaoException
    */
   public function getEthnicRaceList($orderField='ethnic_race_code', $orderBy='ASC') {
      try {
         $q = Doctrine_Query::create()
             ->from('EthnicRace')
             ->orderBy($orderField.' '.$orderBy);

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Save EthnicRace
    * @param EthnicRace $ethnicRace
    * @return boolean
    * @throws DaoException
    */
   public function saveEthnicRace(EthnicRace $ethnicRace) {
      try {
         if( $ethnicRace->getEthnicRaceCode()== '') {
            $idGenService	=	new IDGeneratorService();
            $idGenService->setEntity($ethnicRace);
            $ethnicRace->setEthnicRaceCode($idGenService->getNextID());
         }
         $ethnicRace->save();
         return true;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete EthnicRace
    * @param array() $ethnicRaceList
    * @return boolean
    * @throws DaoException
    */
   public function deleteEthnicRace($ethnicRaceList = array()) {
      try {
         if(is_array($ethnicRaceList)) {
            $q = Doctrine_Query::create()
                ->delete('EthnicRace')
                ->whereIn('ethnic_race_code', $ethnicRaceList );

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
    * Search EthnicRace
    * @param String $searchMode
    * @param String $searchValue
    * @return Collection
    * @throws DaoException
    */
   public function searchEthnicRace($searchMode, $searchValue) {
      try {
         $q = 	Doctrine_Query::create()
            ->from('EthnicRace')
            ->where("$searchMode = ?", trim($searchValue));

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }
}
?>