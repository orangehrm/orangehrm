<?php

class OperationalCountryDao extends BaseDao {

    /**
     *
     * @return Doctrine_Collection
     */
    public function getOperationalCountryList() {
        try {
            $query = Doctrine_Query::create()
                    ->from('OperationalCountry');
            return $query->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param string $countryCode 
     */
    public function getLocationsMappedToOperationalCountry($countryCode) {
        try {
            $query = Doctrine_Query::create()
                    ->from('Location')
                    ->where('country_code = ?', $countryCode);
            return $query->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

}

