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
class AddProjectForm extends BaseForm {

	private $customerService;
	public $numberOfProjectAdmins = 5;

	public function getCustomerService() {
		if (is_null($this->customerService)) {
			$this->customerService = new CustomerService();
			$this->customerService->setCustomerDao(new CustomerDao());
		}
		return $this->customerService;
	}

	public function configure() {

		$this->setWidgets(array(
		    'projectId' => new sfWidgetFormInputHidden(),
		    'customerId' => new sfWidgetFormInputHidden(),
		    'customerName' => new sfWidgetFormInputText(),
		    'projectName' => new sfWidgetFormInputText(),
		    'projectAdminList' => new sfWidgetFormInputHidden(),
		    'description' => new sfWidgetFormTextArea(),
		));

		for ($i = 1; $i <= $this->numberOfProjectAdmins; $i++) {
			$this->setWidget('projectAdmin_' . $i, new sfWidgetFormInputText());
		}

		$this->setValidators(array(
		    'projectId' => new sfValidatorNumber(array('required' => false)),
		    'customerId' => new sfValidatorNumber(array('required' => true)),
		    'customerName' => new sfValidatorString(array('required' => true, 'max_length' => 52)),
		    'projectName' => new sfValidatorString(array('required' => true, 'max_length' => 52)),
		    'projectAdminList' => new sfValidatorString(array('required' => false)),
		    'description' => new sfValidatorString(array('required' => false, 'max_length' => 255)),
		));

		for ($i = 1; $i <= $this->numberOfProjectAdmins; $i++) {
			$this->setValidator('projectAdmin_' . $i, new sfValidatorString(array('required' => false, 'max_length' => 100)));
		}

		$this->widgetSchema->setNameFormat('addProject[%s]');
	}

	public function save() {

		$project = new Project();
		$project->setCustomerId($this->getValue('customerId'));
		$project->setName($this->getValue('projectName'));
		$project->setDescription($this->getValue('description'));
		$project->setDeleted(Project::ACTIVE_PROJECT);
		$project->save();
		$projectId = $project->getProjectId();
		$projectAdminsArray = $this->getValue('projectAdminList');
		$projectAdmins = explode(",", $projectAdminsArray);
		$this->saveProjectAdmins($projectAdmins, $projectId);
	}

	protected function saveProjectAdmins($projectAdmins, $projectId) {

		if (!empty($projectAdmins)) {
			for ($i = 0; $i < count($projectAdmins); $i++) {
				$projectAdmin = new ProjectAdmin();
				$projectAdmin->setProjectId($projectId);
				$projectAdmin->setEmpNumber($projectAdmins[$i]);
				$projectAdmin->save();
			}
		}
	}

	protected function getCustomerList() {

		$list = array("" => "-- " . __('Select') . " --");
		$customerList = $this->getCustomerService()->getAllCustomers();
		foreach ($customerList as $customer) {

			$list[$customer->getCustomerId()] = $customer->getName();
		}
		return $list;
	}

	public function getEmployeeListAsJson() {

		$jsonArray = array();
		$employeeService = new EmployeeService();
		$employeeService->setEmployeeDao(new EmployeeDao());

		$employeeList = $employeeService->getEmployeeList();
		$employeeUnique = array();
		foreach ($employeeList as $employee) {

			if (!isset($employeeUnique[$employee->getEmpNumber()])) {

				$name = $employee->getFirstName() . " " . $employee->getMiddleName();
				$name = trim(trim($name) . " " . $employee->getLastName());

				$employeeUnique[$employee->getEmpNumber()] = $name;
				$jsonArray[] = array('name' => $name, 'id' => $employee->getEmpNumber());
			}
		}

		$jsonString = json_encode($jsonArray);

		return $jsonString;
	}

	public function getCustomerListAsJson() {

		$jsonArray = array();

		$customerList = $this->getCustomerService()->getAllCustomers();


		foreach ($customerList as $customer) {

			$jsonArray[] = array('name' => $customer->getName(), 'id' => $customer->getCustomerId());
		}

		$jsonString = json_encode($jsonArray);

		return $jsonString;
	}

}

?>
