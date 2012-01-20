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
class PayGradeCurrencyForm extends BaseForm {
	
	private $payGradeService;
	public $payGradeId;

	public function getPayGradeService() {
		if (is_null($this->payGradeService)) {
			$this->payGradeService = new PayGradeService();
			$this->payGradeService->setPayGradeDao(new PayGradeDao());
		}
		return $this->payGradeService;
	}
	
	public function configure() {

		$this->payGradeId = $this->getOption('payGradeId');
		
		$this->setWidgets(array(
		    'currencyId' => new sfWidgetFormInputHidden(),
		    'payGradeId' => new sfWidgetFormInputHidden(),
		    'currencyName' => new sfWidgetFormInputText(),
		    'minSalary' => new sfWidgetFormInputText(),
		    'maxSalary' => new sfWidgetFormInputText(),
		));

		$this->setValidators(array(
		    'currencyId' => new sfValidatorString(array('required' => false)),
		    'payGradeId' => new sfValidatorNumber(array('required' => false)),
		    'currencyName' => new sfValidatorString(array('required' => true)),
		    'minSalary' => new sfValidatorNumber(array('required' => false)),
		    'maxSalary' => new sfValidatorNumber(array('required' => false)),
		));

		$this->widgetSchema->setNameFormat('payGradeCurrency[%s]');		
	}
	
	public function save(){
		
		$currencyId = $this->getValue('currencyId');
		$currencyName = $this->getValue('currencyName');
		$temp = explode(" - ", trim($currencyName));
		
		if(!empty ($currencyId)){
			$currency = $this->getPayGradeService()->getCurrencyByCurrencyIdAndPayGradeId($currencyId, $this->payGradeId);
		} else {
			$currency = new PayGradeCurrency();
		}
		
		$currency->setPayGradeId($this->payGradeId);
		$currency->setCurrencyId($temp[0]);
		$currency->setMinSalary(sprintf("%01.2f", $this->getValue('minSalary')));
		$currency->setMaxSalary(sprintf("%01.2f", $this->getValue('maxSalary')));
		$currency->save();
		return $this->payGradeId;
	}
	
}

?>