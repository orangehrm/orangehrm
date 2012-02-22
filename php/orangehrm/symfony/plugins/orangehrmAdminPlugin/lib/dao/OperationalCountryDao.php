<?php

class OperationalCountryDao extends BaseDao {

    /**
     *
     * @return Doctrine_Collection
     */
    public function getOperationalCountryList() {
        try {
            $query = Doctrine_Query::create()
                    ->from('OperationalCountry oc')
                    ->leftJoin('oc.Country c')
                    ->orderBy('c.cou_name');
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
                    ->from('Location l')
                    ->where('l.country_code = ?', $countryCode)
                    ->orderBy('l.name ASC');
            return $query->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

}

