<?php
/**
 * Import DAO Class to save, retrieve, update Import model
 *
 */
class CustomImportDao extends BaseDao {

   /**
    * Get Custom Import List
    * @param String $orderField
    * @param String $orderBy
    * @return Collection
    * @throws DaoException
    */
   public function getCustomImportList($orderField = 'import_id', $orderBy = 'ASC') {
      try {
            $orderBy = strcasecmp($orderBy, 'DESC') === 0 ? 'DESC' : 'ASC';
            $q = Doctrine_Query::create()
			    ->from('CustomImport ci')
			    ->orderBy($orderField . ' ' . $orderBy);

			$importList = $q->execute();
			return  $importList;

      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Saves Custom Import Model Object
    * @param CustomImport $customImport
    * @throws DaoException
    */
   public function saveCustomImport(CustomImport $customImport) {
      try {
         $customImport->save();
         return true;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }

   }

   /**
    * Read Custom Import By the Id
    * @param int $id
    * @return CustomImport
    * @throws DaoException
    */
   public function readCustomImport($id) {
      try {
         $q = Doctrine_Query::create()
			    ->from('CustomImport')
			    ->where("import_id = ?", $id);

			$customImport = $q->fetchOne();
         return $customImport;

      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Search Custom Import
    * @param String $field
    * @param String $value
    * @return Collection
    * @throws DaoException
    */
   public function searchCustomImport($field, $value) {
      try {
         $q = Doctrine_Query::create()
             ->from('CustomImport')
             ->where($field . " = ?", $value);

         $importList = $q->execute();
			return $importList;

      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete Custom Import Model
    * @param int $id
    * @return boolean
    * @throws DaoException
    */
   public function deleteCustomImport($id) {
      try {
         $q = 	Doctrine_Query::create()
				->delete('CustomImport')
				->where('import_id = ?', $id);

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