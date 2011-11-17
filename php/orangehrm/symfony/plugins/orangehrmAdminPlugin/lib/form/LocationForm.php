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

	public function configure() {

		$countries = $this->getCountryList();

		$this->setWidgets(array(
		    'locationId' => new sfWidgetFormInputHidden(),
		    'name' => new sfWidgetFormInputText(),
		    'country' => new sfWidgetFormSelect(array('choices' => $countries)),
		    'stateProvince' => new sfWidgetFormInputText(),
		    'city' => new sfWidgetFormInputText(),
		    'address' => new sfWidgetFormTextArea(),
		    'zipCode' => new sfWidgetFormInputText(),
		    'phone' => new sfWidgetFormInputText(),
		    'fax' => new sfWidgetFormInputText(),
		    'notes' => new sfWidgetFormTextArea(),
		));

		$this->setValidators(array(
		    'locationId' => new sfValidatorNumber(array('required' => false)),
		    'name' => new sfValidatorString(array('required' => true, 'max_length' => 102)),
		    'country' => new sfValidatorString(array('required' => true, 'max_length' => 3)),
		    'stateProvince' => new sfValidatorString(array('required' => false, 'max_length' => 52)),
		    'city' => new sfValidatorString(array('required' => false, 'max_length' => 52)),
		    'address' => new sfValidatorString(array('required' => false, 'max_length' => 256)),
		    'zipCode' => new sfValidatorString(array('required' => false, 'max_length' => 32)),
		    'phone' => new sfValidatorString(array('required' => false, 'max_length' => 32)),
		    'fax' => new sfValidatorString(array('required' => false, 'max_length' => 32)),
		    'notes' => new sfValidatorString(array('required' => false, 'max_length' => 256)),
		));


		$this->widgetSchema->setNameFormat('location[%s]');
	}

	/**
	 * Returns Country List
	 * @return array
	 */
	private function getCountryList() {
		$list = array(0 => "-- " . __('Select') . " --");
		$countries = $this->getCountryService()->getCountryList();
		foreach ($countries as $country) {
			$list[$country->cou_code] = $country->cou_name;
		}
		return $list;
	}

}

?>
