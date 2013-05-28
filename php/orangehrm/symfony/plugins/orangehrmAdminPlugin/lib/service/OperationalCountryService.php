<?php

class OperationalCountryService extends BaseService {

    protected $operationalCountryDao;

    /**
     *
     * @return OperationalCountryDao
     */
    public function getOperationalCountryDao() {
        if (!($this->operationalCountryDao instanceof OperationalCountryDao)) {
            $this->operationalCountryDao = new OperationalCountryDao();
        }
        return $this->operationalCountryDao;
    }

    /**
     *
     * @param OperationalCountryDao $dao 
     */
    public function setOperationalCountryDao(OperationalCountryDao $dao) {
        $this->operationalCountryDao = $dao;
    }

    /**
     * 
     * @return Doctrine_Collection
     */
    public function getOperationalCountryList() {
        try {
            return $this->getOperationalCountryDao()->getOperationalCountryList();
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage());
        }
    }

    /**
     *
     * @param OperationalCountry $country 
     * @return Doctrine_Collection
     */
    public function getLocationsMappedToOperationalCountry(OperationalCountry $country) {
        try {
            return $this->getOperationalCountryDao()->getLocationsMappedToOperationalCountry($country->getCountryCode());
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage());
        }
    }
    
    /**
     * Get operational countries for the given locations
     * 
     * @param Array $locationIds Array of location IDs
     * @return Doctrine_Collection of Operational countries
     */
    public function getOperationalCountriesForLocations($locationIds) {
        return $this->getOperationalCountryDao()->getOperationalCountriesForLocations($locationIds);     
    }    

    public function getOperationalCountriesForEmployees($empNumbers) {
        return $this->getOperationalCountryDao()->getOperationalCountriesForEmployees($empNumbers);
    }
}
