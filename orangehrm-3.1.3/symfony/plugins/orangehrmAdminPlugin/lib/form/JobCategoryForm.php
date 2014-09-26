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

class JobCategoryForm extends BaseForm {
	
	private $jobCatService;

	public function getJobCategoryService() {
		if (is_null($this->jobCatService)) {
			$this->jobCatService = new JobCategoryService();
			$this->jobCatService->setJobCategoryDao(new JobCategoryDao());
		}
		return $this->jobCatService;
	}
	
	public function configure() {

		$this->setWidgets(array(
		    'jobCategoryId' => new sfWidgetFormInputHidden(),
		    'name' => new sfWidgetFormInputText(),
		));

		$this->setValidators(array(
		    'jobCategoryId' => new sfValidatorNumber(array('required' => false)),
		    'name' => new sfValidatorString(array('required' => true, 'max_length' => 52, 'trim' => true)),
		));

		$this->widgetSchema->setNameFormat('jobCategory[%s]');
				
	}
	
	public function save(){
		
		$jobCatId = $this->getValue('jobCategoryId');
		if(!empty ($jobCatId)){
			$jobCat = $this->getJobCategoryService()->getJobCategoryById($jobCatId);
		} else {
			$jobCat = new JobCategory();
		}
		$jobCat->setName($this->getValue('name'));
		$jobCat->save();
	}
	
	public function getJobCategoryListAsJson() {
		
		$list = array();
		$jobCatList = $this->getJobCategoryService()->getJobCategoryList();
		foreach ($jobCatList as $jobCat) {
			$list[] = array('id' => $jobCat->getId(), 'name' => $jobCat->getName());
		}
		return json_encode($list);
	}
}

?>
