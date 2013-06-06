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
class EmploymentStatusForm extends BaseForm {

    protected $empStatusService;
    
	public function getEmploymentStatusService() {
		if (is_null($this->empStatusService)) {
			$this->empStatusService = new EmploymentStatusService();
			$this->empStatusService->setEmploymentStatusDao(new EmploymentStatusDao());
		}
		return $this->empStatusService;
	}

	public function configure() {

		$this->setWidgets(array(
		    'empStatusId' => new sfWidgetFormInputHidden(),
		    'name' => new sfWidgetFormInputText(),
		));

		$this->setValidators(array(
		    'empStatusId' => new sfValidatorNumber(array('required' => false)),
		    'name' => new sfValidatorString(array('required' => true, 'max_length' => 52)),
		));

		$this->widgetSchema->setNameFormat('empStatus[%s]');
		
	}

	public function save() {

		$empStatusId = $this->getValue('empStatusId');
		if (!empty($empStatusId)) {
			$empStatus = $this->getEmploymentStatusService()->getEmploymentStatusById($empStatusId);
		} else {
			$empStatus = new EmploymentStatus();
		}
		$empStatus->setName($this->getValue('name'));
		$empStatus->save();
	}

	public function getEmploymentStatusListAsJson() {

		$list = array();
		$empStatusList = $this->getEmploymentStatusService()->getEmploymentStatusList();
		foreach ($empStatusList as $empStatus) {
			$list[] = array('id' => $empStatus->getId(), 'name' => $empStatus->getName());
		}
		return json_encode($list);
	}

}

?>
