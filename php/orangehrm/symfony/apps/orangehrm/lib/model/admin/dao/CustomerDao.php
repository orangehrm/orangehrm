<?php
/**
 * CustomerDao class for CRUD operation
 *
 * @author Sujith T
 */
class CustomerDao extends BaseDao {

   /**
    * Get Customer List
    * @param String $orderField
    * @param String $orderBy
    * @returns Colection
    * @throws DaoException
    */
   public function getCustomerList($orderField = 'customer_id', $orderBy = 'ASC') {
      try {
	    	$q = Doctrine_Query::create()
			    ->from('Customer')
			    ->where('deleted = ?', 0)
			    ->orderBy($orderField.' '.$orderBy);

			return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
    }

    /**
     * Save Customer
     * @param Customer customer
     * @returns boolean
     * @throws DaoException, DataDuplicationException
     */
    public function saveCustomer(Customer $customer) {
      try {
         $q = Doctrine_Query::create()
			    ->from('Customer m')
             ->where('m.name = ?', $customer->name);

	    	if(!empty($customer->customer_id)) {
            $q->andWhere('m.customer_id <> ?', $customer->customer_id) ;
         }

         if ($q->count() > 0) {
            throw new DataDuplicationException("Cannot save customer due to saving duplicated data");
         }

			if($customer->getCustomerId() == '') {
	        	$idGenService	=	new IDGeneratorService();
				$idGenService->setEntity($customer);
				$customer->setCustomerId($idGenService->getNextID());
			}
        	$customer->save();
        	return true ;
      } catch(Doctrine_Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Delete Customer
    * @param array() $customerList
    * @returns boolean
    * @throws DaoException
    */
   public function deleteCustomer($customerList = array()) {
      try {
         if(is_array($customerList)) {
	        	$q = Doctrine_Query::create()
					    ->update('Customer')
                   ->set('deleted', '?', true)
					    ->whereIn('customer_id', $customerList );

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
    * Search Customer
    * @param String $searchMode
    * @param String $searchValue
    * @returns Customer Collection/Customer
    * @throws DaoException
    */
   public function searchCustomer($searchMode, $searchValue) {
      try {
        	$q = 	Doctrine_Query::create( )
				->from('Customer')
				->where("$searchMode = ?", trim($searchValue));

			return $q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * ReadCustomer by Id. This need to be refactored to read from DC::create function later
    * @param int $id
    * @returns Customer
    * @throws DaoException
    */
   public function readCustomer($id) {
      try {
         return Doctrine::getTable('Customer')->find($id);
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }
}
?>
