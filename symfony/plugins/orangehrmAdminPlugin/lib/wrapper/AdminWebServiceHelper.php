<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminWebServiceHelper
 *
 * @author nirmal
 */
class AdminWebServiceHelper {

    protected $jobTitleService;
    protected $locationService;

    /**
     * Get Job title service
     * @return JobTitleService
     */
    public function getJobTitleService() {
        if (is_null($this->jobTitleService)) {
            $this->jobTitleService = new JobTitleService();
        }
        return $this->jobTitleService;
    }

    /**
     * Get location service
     * @return LocationService
     */
    public function getLocationService() {
        if (is_null($this->locationService)) {
            $this->locationService = new LocationService();
        }
        return $this->locationService;
    }

    /**
     * Get Job title list
     * @return type
     */
    public function getJobTitleList() {
        $jobTitleArray = array();
        $jobTitleList = $this->getJobTitleService()->getJobTitleList();

        foreach ($jobTitleList as $jobTitle) {
            $jobTitleDetailsArray = array('id' => $jobTitle->getId(), 'jobTitleName' => $jobTitle->getJobTitleName());
            $jobTitleArray[] = $jobTitleDetailsArray;
        }
        return $jobTitleArray;
    }

    /**
     * Get Location list
     * @param type $showAll
     * @return type
     */
    public function getLocationList($showAll = true) {
        $locationList = array();
        $locations = $this->getLocationService()->getLocationList();

        $accessibleLocations = $this->getAccessibleLocations();

        foreach ($locations as $location) {
            if ($showAll || in_array($location->id, $accessibleLocations)) {
                $locationDetailsArray = array(
                    'id' => $location->getId(),
                    'locationName' => $location->getName(),
                    'country_code' => $location->getCountryCode(),
                    'province' => $location->getProvince(),
                    'city' => $location->getCity(),
                    'address' => $location->getAddress()
                );
                $locationList[] = $locationDetailsArray;
            }
        }

        return $locationList;
    }
    
    public function getAccessibleLocations() {
        return UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntityIds('Location', BasicUserRoleManager::OPERATION_VIEW);
    }

}
