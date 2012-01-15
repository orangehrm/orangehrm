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

}
