<?php

class OperationalCountryDao extends BaseDao {

    public function getOperationalCountryList() {
        try {
            $query = Doctrine_Query::create()
                    ->from('OperationalCountry');
            return $query->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

}

