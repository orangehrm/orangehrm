<?php
/*
// OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
// all the essential functionalities required for any enterprise.
// Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

// OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
// the GNU General Public License as published by the Free Software Foundation; either
// version 2 of the License, or (at your option) any later version.

// OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
// without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU General Public License for more details.

// You should have received a copy of the GNU General Public License along with this program;
// if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
// Boston, MA  02110-1301, USA
*/

/**
 * Form class for employee define kpi list in Performance
 */
class EmployeeKpiDefineForm extends BaseForm {
            
    public function configure() {

		$jobTitle = new JobTitle();
		
		$kpiDefinedJobTitles = $jobTitle->getJobTitlesDefined();
		
		if (empty($kpiDefinedJobTitles)) {
			$choices = array('-1'=> '- Select -');
		} else {
			foreach ($kpiDefinedJobTitles as $key => $val) {
				foreach ($val as $jobTitleId => $jobTitleName) {
					$arrFinal[$jobTitleId] = $jobTitleName;
				}
			}
			$choices = array('-1'=> '- Select -') + $arrFinal;
		}
		
        $this->setWidgets(array(
           	'JobTitle' =>  new sfWidgetFormDoctrineChoice(array('model' => 'JobTitle', 'add_empty' => '- Select -')),
			'JobTitleFrom' => new sfWidgetFormSelect(array('choices' => $choices)),
			'KpiDescription' => new sfWidgetFormTextarea(),
			'MinRate' => new sfWidgetFormInputText(),
			'MaxRate' => new sfWidgetFormInputText(),
			'DefaultScale' => new sfWidgetFormInputCheckbox (),
			'isCopy' => new sfWidgetFormInputHidden(),
			'KpiId' => new sfWidgetFormInputHidden(),
        ));
        
        $this->widgetSchema->setNameFormat('empdefinekpi[%s]');
		
        $this->setValidators(array(
			'JobTitle' =>  new sfValidatorDoctrineChoice(array('model' => 'JobTitle', 'column' => 'jobtit_code ', 'required' => true), array('required' => 'Please select Job Title')),
			'JobTitleFrom' => new sfValidatorString(array('required' => false)),			  
            'KpiDescription' => new sfValidatorString(array('required' => true, 'max_length' => 200), array('required' => 'Please enter KPI description', 'max_length' => 'Please enter KPI description less than 200 characters')),
			'MinRate' => new sfValidatorNumber(array('required' => false)),
			'MaxRate' => new sfValidatorNumber(array('required' => false)), 
			'DefaultScale' => new sfValidatorString(array('required' => false)),
			'isCopy' => new sfValidatorString(array('required' => false)),
			'KpiId' => new sfValidatorString(array('required' => false)),
        ));	
    	$this->validatorSchema->setPostValidator(
      	new sfValidatorCallback(array('callback' => array($this, 'checkMinMaxRates')))
    	);   	
    }
    /**
     * check if the minimum rate is higher than the maximum value
     * @param $validator
     * @param $values
     * @return array
     */
	public function checkMinMaxRates($validator, $values){
		
    	if (($values['MinRate'] > $values['MaxRate']) && (!is_null($values['MaxRate']) && !is_null($values['MinRate']))){
      		throw new sfValidatorError($validator, 'Minimum Scale is higher than Maximum Scale. Please correct the values properly.');   		
    	} else if((is_null($values['MinRate'])) && (!is_null($values['MaxRate']))) {
    		throw new sfValidatorError($validator, 'Minimum Scale is not entered.');
    	} else if((is_null($values['MaxRate'])) && (!is_null($values['MinRate']))) {
    		throw new sfValidatorError($validator, 'Maximum Scale is not entered.');
    	} else if($values['MinRate'] == $values['MaxRate'] && (!is_null($values['MaxRate']) && !is_null($values['MinRate']))) {
    		throw new sfValidatorError($validator, 'Enter a higher Maximum Scale.');
    	//} else if($values['MinRate'] == $values['MaxRate'] || ($values['MinRate'] == 0)) {
    		//throw new sfValidatorError($validator, 'Enter a higher Maximum Scale.');
    	} else {
    		return $values;
    	}
  	}
}

