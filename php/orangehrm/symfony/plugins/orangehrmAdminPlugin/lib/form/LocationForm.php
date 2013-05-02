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
 *
 */
class LocationForm extends BaseForm {

	public $locationId;
	private $locationService;
	private $countryService;
	public $edited = false;
	
	/**
	 * Returns Country Service
	 * @returns CountryService
	 */
	public function getCountryService() {
		if (is_null($this->countryService)) {
			$this->countryService = new CountryService();
		}
		return $this->countryService;
	}
	
	public function getLocationService() {
		if (is_null($this->locationService)) {
			$this->locationService = new LocationService();
			$this->locationService->setLocationDao(new LocationDao());
		}
		return $this->locationService;
	}

	public function configure() {

		$this->locationId = $this->getOption('locationId');
		$countries = $this->getCountryList();
		$states = $this->getStatesList();

		$this->setWidgets(array(
		    'locationId' => new sfWidgetFormInputHidden(),
		    'name' => new sfWidgetFormInputText(array(), array('class' => 'formInput')),
		    'country' => new sfWidgetFormSelect(array('choices' => $countries), array('class' => 'formInput')),
		    'state' => new sfWidgetFormSelect(array('choices' => $states), array('class' => 'formInput')),
		    'province' => new sfWidgetFormInputText(array(), array('class' => 'formInput')),
		    'city' => new sfWidgetFormInputText(array(), array('class' => 'formInput')),
		    'address' => new sfWidgetFormTextArea(array(), array('class' => 'formInput')),
		    'zipCode' => new sfWidgetFormInputText(array(), array('class' => 'formInput')),
		    'phone' => new sfWidgetFormInputText(array(), array('class' => 'formInput')),
		    'fax' => new sfWidgetFormInputText(array(), array('class' => 'formInput')),
		    'notes' => new sfWidgetFormTextArea(array(), array('class' => 'formInput')),
		));

		$this->setValidators(array(
		    'locationId' => new sfValidatorNumber(array('required' => false)),
		    'name' => new sfValidatorString(array('required' => true, 'max_length' => 102)),
		    'country' => new sfValidatorString(array('required' => true, 'max_length' => 3)),
		    'state' => new sfValidatorString(array('required' => false, 'max_length' => 52)),
		    'province' => new sfValidatorString(array('required' => false, 'max_length' => 52)),
		    'city' => new sfValidatorString(array('required' => false, 'max_length' => 52)),
		    'address' => new sfValidatorString(array('required' => false, 'max_length' => 256)),
		    'zipCode' => new sfValidatorString(array('required' => false, 'max_length' => 32)),
		    'phone' => new sfValidatorString(array('required' => false, 'max_length' => 32)),
		    'fax' => new sfValidatorString(array('required' => false, 'max_length' => 32)),
		    'notes' => new sfValidatorString(array('required' => false, 'max_length' => 256, 'trim' => true)),
		));


		$this->widgetSchema->setNameFormat('location[%s]');
		
		if ($this->locationId != null) {
			$this->setDefaultValues($this->locationId);
		}
        
        $this->getWidgetSchema()->setLabels($this->getFormLabels());

	}
	
	private function setDefaultValues($locationId) {

		$location = $this->getLocationService()->getLocationById($this->locationId);
		$this->setDefault('locationId', $locationId);
		$this->setDefault('name', $location->getName());
		$this->setDefault('country', $location->getCountryCode());
		if($location->getCountryCode() == 'US') {
			$this->setDefault('state', $location->getProvince());
		} else {
			$this->setDefault('province', $location->getProvince());
		}
		$this->setDefault('city', $location->getCity());
		$this->setDefault('address', $location->getAddress());
		$this->setDefault('zipCode', $location->getZipCode());
		$this->setDefault('phone', $location->getPhone());
		$this->setDefault('fax', $location->getFax());
		$this->setDefault('notes', $location->getNotes());
		
	}

	/**
	 * Returns Country List
	 * @return array
	 */
	private function getCountryList() {
		$list = array("" => "-- " . __('Select') . " --");
		$countries = $this->getCountryService()->getCountryList();
		foreach ($countries as $country) {
			$list[$country->cou_code] = $country->cou_name;
		}
		return $list;
	}

	/**
	 * Returns States List
	 * @return array
	 */
	private function getStatesList() {
		$list = array("" => "-- " . __('Select') . " --");
		$states = $this->getCountryService()->getProvinceList();
		foreach ($states as $state) {
			$list[$state->province_code] = $state->province_name;
		}
		return $list;
	}

	public function save() {

		$locationId = $this->getValue('locationId');
		if(empty($locationId)){
			$location = new Location();
		} else {
			$this->edited = true;
			$location = $this->getLocationService()->getLocationById($locationId);
		}
		$location->setName($this->getValue('name'));
		$country = $this->getValue('country');
		$location->setCountryCode($country);
		if ($country == 'US') {
			$location->setProvince($this->getValue('state'));
		} else {
			$location->setProvince($this->getValue('province'));
		}
		$location->setCity($this->getValue('city'));
		$location->setAddress($this->getValue('address'));
		$location->setZipCode($this->getValue('zipCode'));
		$location->setPhone($this->getValue('phone'));
		$location->setFax($this->getValue('fax'));
		$location->setNotes($this->getValue('notes'));
		$location->save();
		
		return $location->getId();
	}

	public function getLocationListAsJson() {
		
		$list = array();
		$locationList = $this->getLocationService()->getLocationList();
		foreach ($locationList as $location) {
			$list[] = array('id' => $location->getId(), 'name' => $location->getName());
		}
		return json_encode($list);
	}
    
    /**
     * 
     * @return string 
     */
    public function getFormLabels() {
        $requiredMarker = ' <em>*</em>';
        $labels = array(
            'name' => __('Name') . $requiredMarker,
            'country' => __('Country') . $requiredMarker,
            'state' => __('State'),
		    'province' => __('State/Province'),
		    'city' => __('City'),
		    'address' => __('Address'),
		    'zipCode' => __('Zip/Postal Code'),
		    'phone' => __('Phone'),
		    'fax' => __('Fax'),
		    'notes' => __('Notes'),
        );
        return $labels;
    }
}

?>
