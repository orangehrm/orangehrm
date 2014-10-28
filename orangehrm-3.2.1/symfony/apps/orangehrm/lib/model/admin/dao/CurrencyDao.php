<?php

/**
 * Currency DAO to execute CRUD operations
 *
 */
class CurrencyDao extends BaseDao {
   
    /**
     * Retrieve All Currency List
     * @param bool $asArray
     * @throws DaoException
     */
    public function getCurrencyList($asArray = false) {
        try {
            $hydrateMode = ($asArray) ? Doctrine::HYDRATE_ARRAY : Doctrine::HYDRATE_RECORD;
            $q = Doctrine_Query::create()
                    ->from('CurrencyType c')
                    ->orderBy('c.currency_name');

            return $q->execute(array(), $hydrateMode);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

}

?>