<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
class LocationService extends BaseService {

    private $locationDao;

    /**
     * Construct
     */
    public function __construct() {
        $this->locationDao = new LocationDao();
    }

    /**
     *
     * @return <type>
     */
    public function getLocationDao() {
        if (!($this->locationDao instanceof LocationDao)) {
            $this->locationDao = new LocationDao();
        }
        return $this->locationDao;
    }

    /**
     *
     * @param LocationDao $locationDao 
     */
    public function setLocationDao(LocationDao $locationDao) {
        $this->locationDao = $locationDao;
    }

    /**
     * Get Location by id
     * 
     * @param type $locationId
     * @return type 
     */
    public function getLocationById($locationId) {
        return $this->locationDao->getLocationById($locationId);
    }

    /**
     * 
     * Search location by project name, city and country.
     * 
     * @param type $srchClues
     * @return type 
     */
    public function searchLocations($srchClues) {
        return $this->locationDao->searchLocations($srchClues);
    }

    /**
     *
     * Get location count of the search results.
     *
     * @param type $srchClues
     * @return type 
     */
    public function getSearchLocationListCount($srchClues) {
        return $this->locationDao->getSearchLocationListCount($srchClues);
    }

    /**
     * Get total number of employees in a location.
     * 
     * @param type $locationId
     * @return type 
     */
    public function getNumberOfEmplyeesForLocation($locationId) {
        return $this->locationDao->getNumberOfEmplyeesForLocation($locationId);
    }

    /**
     * Get all locations
     * 
     * @return type 
     */
    public function getLocationList() {
        return $this->locationDao->getLocationList();
    }

    /**
     * Get LocationIds for Employees with the given employee numbers
     * 
     * @param Array $empNumbers Array of employee numbers
     * @return Array of locationIds of the given employees
     */
    public function getLocationIdsForEmployees($empNumbers) {
        return $this->getLocationDao()->getLocationIdsForEmployees($empNumbers);
    }    
}


