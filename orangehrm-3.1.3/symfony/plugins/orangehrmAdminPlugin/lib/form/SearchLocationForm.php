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
class SearchLocationForm extends BaseForm {

	private $countryService;

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
		    'name' => new sfWidgetFormInputText(),
		    'city' => new sfWidgetFormInputText(),
		    'country' => new sfWidgetFormSelect(array('choices' => $countries)),
		));

		$this->setValidators(array(
		    'name' => new sfValidatorString(array('required' => false, 'max_length' => 100)),
		    'city' => new sfValidatorString(array('required' => false, 'max_length' => 100)),
		    'country' => new sfValidatorString(array('required' => false, 'max_length' => 100)),
		));

        $this->getWidgetSchema()->setLabels($this->getFormLabels());
		$this->widgetSchema->setNameFormat('searchLocation[%s]');

	}

	public function setDefaultDataToWidgets($searchClues) {
		$this->setDefault('name', $searchClues['name']);
		$this->setDefault('city', $searchClues['city']);
		$this->setDefault('country', $searchClues['country']);
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
     *
     * @return array
     */
    protected function getFormLabels() {
        $labels = array(
            'name' => __('Location Name'),
            'city' => __('City'),
            'country' => __('Country')
        );
        return $labels;
    }

}

?>
