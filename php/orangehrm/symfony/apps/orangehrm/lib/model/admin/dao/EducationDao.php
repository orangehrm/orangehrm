<?php
/**
 * EducationDao for CRUD operations
 *
 * @author Sujith T
 */
class EducationDao extends BaseDao {

   /**
    * Retrieve EducationList
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws DaoException
    */
   public function getEducationList($orderField='eduCode', $orderBy='ASC') {
      try {
         $q = Doctrine_Query::create()
          ->from('Education edu')
          ->orderBy($orderField.' '.$orderBy);

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Save Education
    * @param Education education
    * @returns boolean
    * @throws DaoException, DataDuplicationException
    */
   public function saveEducation(Education $education) {
      try {
         $q = Doctrine_Query::create()
             ->from('Education e')
             ->where('e.edu_uni = ?', $education->edu_uni)
             ->andWhere('e.edu_deg = ?', $education->edu_deg);

         if (!empty($education->eduCode)) {
            $q->andWhere('e.eduCode <> ?', $education->eduCode) ;
         }

         if ($q->count() > 0) {
            throw new DataDuplicationException("Saving education failed due to duplicated record");
         }

         if($education->getEduCode() == '') {
            $idGenService	=	new IDGeneratorService();
            $idGenService->setEntity($education);
            $education->setEduCode( $idGenService->getNextID() );
         }
         $education->save();
         return true ;
      } catch(Doctrine_Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Deletes Education
    * @param array() $educationList
    * @returns boolean
    * @throws DaoException
    */
   public function deleteEducation($educationList = array()) {
      try {
         if(is_array($educationList)) {
            $q = Doctrine_Query::create()
                   ->delete('Education')
                   ->whereIn('eduCode', $educationList );

            $numDeleted = $q->execute();
            if($numDeleted > 0) {
               return true;
            }
         }
         return false;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Get Education by Id
    * @param int $id
    * @returns Education
    * @throws DaoException
    */
   public function readEducation($id) {
      try {
         return Doctrine::getTable('Education')->find($id);
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Search Education
    * @param String $searchMode
    * @param String $searchValue
    * @returns Collection/Education
    * @throws DaoException
    */
   public function searchEducation($searchMode, $searchValue ) {
      try {
         $q = Doctrine_Query::create()
            ->from('Education')
            ->where("$searchMode = ?",trim($searchValue));

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Get Licenses List
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws DaoException
    */
   public function getLicensesList($orderField = 'licenses_code', $orderBy='ASC') {
      try {
         $q = Doctrine_Query::create()
             ->from('Licenses')
             ->orderBy($orderField.' '.$orderBy);

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Save License
    * @param Licenses $licenses
    * @returns boolean
    * @throws DaoException, DataDuplicationException
    */
   public function saveLicenses(Licenses $licenses) {
      try {
         $q = Doctrine_Query::create()
             ->from('Licenses l')
             ->where('l.licenses_desc = ?', $licenses->licenses_desc);

         if (!empty($licenses->licenses_code)) {
            $q->andWhere('l.licenses_code <> ?', $licenses->licenses_code) ;
         }

         if ($q->count() > 0) {
            throw new DataDuplicationException("License saving failed due to duplicated data");
         }

         if($licenses->getLicensesCode() == '') {
            $idGenService	=	new IDGeneratorService();
            $idGenService->setEntity($licenses);
            $licenses->setLicensesCode($idGenService->getNextID());
         }

         $licenses->save();
         return true;
      } catch( Doctrine_Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete License
    * @param array() $licensesList
    * @returns boolean
    * @throws DaoException
    */
   public function deleteLicenses($licensesList = array()) {
      try {
         if(is_array($licensesList )) {
            $q = Doctrine_Query::create()
                   ->delete('Licenses')
                   ->whereIn('licenses_code', $licensesList);

            $numDeleted = $q->execute();
            if($numDeleted > 0) {
               return true;
            }
         }
         return false;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete License
    * @param String $searchMode
    * @param String $searchValue
    * @returns Collection/Licenses
    * @throws DaoException
    */
   public function searchLicenses($searchMode, $searchValue) {
      try {
         $q = 	Doctrine_Query::create( )
            ->from('Licenses')
            ->where("$searchMode = ?",trim($searchValue));

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Get by Licenses by Id
    * @param int $id
    * @returns Licenses
    * @throws DaoException
    */
   public function readLicenses($id) {
      try {
         return Doctrine::getTable('Licenses')->find($id);
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }
}
?>