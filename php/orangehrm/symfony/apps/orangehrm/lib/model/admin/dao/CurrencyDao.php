<?php
/**
 * Currency DAO to execute CRUD operations
 * @author Sujith T
 *
 */
class CurrencyDao extends BaseDao {
   /**
    * Retrieve All Currency List
    * @throws DaoException
    */
   public function getCurrencyList() {
      try {
			$q = Doctrine_Query::create()
			    ->from('CurrencyType c')
			    ->orderBy('c.currency_name');

			return $q->execute();
		} catch(Exception $e) {
			throw new DaoException( $e->getMessage());
		}
   }
}
?>