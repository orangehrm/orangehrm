<?php
/**
 * SkillDao to perform Skill model object's CRUD operation
 *
 * @author Sujith T
 */
class SkillDao extends BaseDao {

   /**
    * Retrieve SkillList
    * @param String $orderField
    * @param String $orderBy
    * @returns Collection
    * @throws DaoException
    */
   public function getSkillList($orderField='skill_code', $orderBy='ASC') {
      try {
         $q = Doctrine_Query::create()
             ->from('Skill')
             ->orderBy($orderField.' '.$orderBy);

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Save Skill
    * @param Skill $skill
    * @returns boolean
    * @throws DaoException
    */
   public function saveSkill(Skill $skill) {
      try {
         if( $skill->getSkillCode() == '') {
            $idGenService	=	new IDGeneratorService();
            $idGenService->setEntity($skill);
            $skill->setSkillCode($idGenService->getNextID());
         }
         $skill->save();
         return true ;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete Skill
    * @param array() $skillList
    * @returns boolean
    * @throws DaoException
    */
   public function deleteSkill($skillList = array()) {
      try {
         if(is_array($skillList)) {
            $q = Doctrine_Query::create()
               ->delete('Skill')
               ->whereIn('skill_code', $skillList);

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
    * Search Skill
    * @param String $searchMode
    * @param String $searchValue
    * @returns Collection
    * @throws DaoException
    */
   public function searchSkill($searchMode, $searchValue) {
      try {
         $q = 	Doctrine_Query::create()
            ->from('Skill')
            ->where("$searchMode = ?", trim($searchValue));

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }
   
   /**
    * Reads Skill by a given Id
    * @param String $id
    * @returns Skill
    * @throws DaoException
    */
   public function readSkill($id) {
      try {
         return Doctrine::getTable('Skill')->find($id);
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * List Languages
    * @param String $searchMode
    * @param String $searchValue
    * @returns Collection
    * @throws DaoException
    */
   public function getLanguageList($orderField='lang_code', $orderBy='ASC') {
      try {
         $q = Doctrine_Query::create()
             ->from('Language')
             ->orderBy($orderField.' '.$orderBy);

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Save Language
    * @param Language $language
    * @returns boolean
    * @throws DaoException
    */
   public function saveLanguage(Language $language) {
      try {
         if($language->getLangCode() == '') {
            $idGenService	=	new IDGeneratorService();
            $idGenService->setEntity($language);
            $language->setLangCode( $idGenService->getNextID());
         }
         $language->save();
         return true;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete Language
    * @param array() $languageList
    * @returns boolean
    * @throws DaoException
    */
   public function deleteLanguage($languageList = array()) {
      try {
         if(is_array($languageList)) {
            $q = Doctrine_Query::create()
               ->delete('Language')
               ->whereIn('lang_code', $languageList);

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
    * Search Language
    * @param String $searchMode
    * @param String $searchValue
    * @returns Collection
    * @throws DaoException
    */
   public function searchLanguage($searchMode, $searchValue) {
      try {
         $q = 	Doctrine_Query::create()
            ->from('Language')
            ->where("$searchMode = ?", trim($searchValue));

         return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Read Language
    * @param int $id
    * @returns Language must refactor this to return the model object
    * @throws DaoException
    */
   public function readLanguagee($id) {
      try {
         return Doctrine::getTable('Language')->find($id);
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }
}
?>
