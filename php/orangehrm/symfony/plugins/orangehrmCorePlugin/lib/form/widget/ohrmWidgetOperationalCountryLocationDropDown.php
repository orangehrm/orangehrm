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

/**
 * Widget that displays countries and locations within the country
 * in a single drop down
 *
 */
class ohrmWidgetOperationalCountryLocationDropDown extends ohrmWidgetSelectableGroupDropDown {
    
    private $operationalCountryService;
    
    private $choices = null;
  
    protected function configure($options = array(), $attributes = array()) {
                
        parent::configure($options, $attributes);
        
        // Parent requires the 'choices' option.
        $this->addOption('choices', array());

    }
    
    /**
     * Get array of operational country and location choices
     */
    public function getChoices() {
        
        if (is_null($this->choices)) {
           
            $operationalCountries = $this->getOperationalCountryService()->getOperationalCountryList();
            
            $manager = UserRoleManagerFactory::getUserRoleManager();
            
            $accessibleCountryIds = $manager->getAccessibleEntityIds('OperationalCountry');
            
            $user = sfContext::getInstance()->getUser();
            
            // Special case for supervisor - can see all operational countries
            $showAll = false;
            if ($user->getAttribute('auth.isSupervisor')) {
                $showAll = true;
            }

            $choices = array();

            foreach ($operationalCountries as $operationalCountry) {

                $countryId = $operationalCountry->getId();
                
                if ($showAll || in_array($countryId, $accessibleCountryIds)) {
                    $country = $operationalCountry->getCountry();                

                    $locations = $country->getLocation();

                    if (count($locations) > 0) {
                        $locationChoices = array();
                        foreach ($locations as $location) {
                            $locationChoices[$location->getId()] = $location->getName();
                        }
                        asort($locationChoices);
                        $choices[$country->getCouName()] = $locationChoices;
                    }
                }

            }        
            $this->choices = $choices;            
        }
        
        return $this->choices;               
    }
    
    public function getValidValues() {
        $choices = $this->getChoices();
        return array_keys($choices);
    }
    
    
    /**
     *
     * @param OperationalCountryService $service 
     */
    public function setOperationalCountryService(OperationalCountryService $service) {
        $this->operationalCountryService = $service;
    }
    
    /**
     * 
     * @return OperationalCountryService
     */
    public function getOperationalCountryService() {
       if (!($this->operationalCountryService instanceof OperationalCountryService)) {
           $this->operationalCountryService = new OperationalCountryService();
       }
       return $this->operationalCountryService;
    }    
}

