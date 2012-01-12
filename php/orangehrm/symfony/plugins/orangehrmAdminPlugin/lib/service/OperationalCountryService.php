<?php

class OperationalCountryService  {
    
    protected $operationalCountryDao;
    
    /**
     *
     * @return OperationalCountryDao
     */
    public function getOperationalCountryDao() {
        return $this->operationalCountryDao;
    }
    
    /**
     *
     * @param OperationalCountryDao $dao 
     */
    public function getOperationalCountryDao(OperationalCountryDao $dao) {
        $this->operationalCountryDao = $dao;
    }

    /**
     * 
     * @return Doctrine_Collection
     */
    public function getOperationalCountryList() {
        
    }
}
