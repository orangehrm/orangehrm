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
    
    /**
     * Get operational countries for the given locations
     * 
     * @param Array $locationIds Array of location IDs
     * @return Doctrine_Collection of Operational countries
     * @throws DaoException
     */
    public function getOperationalCountriesForLocations($locationIds) {
        try {
            if (empty($locationIds)) {
                return new Doctrine_Collection('OperationalCountry');
            } else {
                $query = Doctrine_Query::create()
                        ->from('OperationalCountry oc')
                        ->leftJoin('oc.Country c')
                        ->leftJoin('c.Location l')
                        ->whereIn('l.id', $locationIds)
                        ->orderBy('c.cou_name ASC');
                return $query->execute();
            }
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }        
    }
    
    public function getOperationalCountriesForEmployees($empNumbers) {
        try {
            if (empty($empNumbers)) {
                return new Doctrine_Collection('OperationalCountry');
            } else {
                $query = Doctrine_Query::create()
                        ->from('OperationalCountry oc')
                        ->leftJoin('oc.Country c')
                        ->leftJoin('c.Location l')
                        ->leftJoin('l.employees e')
                        ->whereIn('e.emp_number', $empNumbers)
                        ->orderBy('c.cou_name ASC');
                return $query->execute();
            }
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }          
    }

}

