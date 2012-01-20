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

class AddProjectActivityForm extends BaseForm {
	
	private $projectService;
	public $edited = false;

	public function getProjectService() {
		if (is_null($this->projectService)) {
			$this->projectService = new ProjectService();
			$this->projectService->setProjectDao(new ProjectDao());
		}
		return $this->projectService;
	}
	
	public function configure() {

		$this->setWidgets(array(
		    'projectId' => new sfWidgetFormInputHidden(),
		    'activityId' => new sfWidgetFormInputHidden(),
		    'activityName' => new sfWidgetFormInputText(),
		    
		));

		$this->setValidators(array(
		    'projectId' => new sfValidatorNumber(array('required' => true)),
		    'activityId' => new sfValidatorNumber(array('required' => false)),
		    'activityName' => new sfValidatorString(array('required' => true, 'max_length' => 102)),
		    
		));

		$this->widgetSchema->setNameFormat('addProjectActivity[%s]');

	}
	
	public function save(){
		
		$projectId = $this->getValue('projectId');
		$activityId = $this->getValue('activityId');
		
		if(!empty ($activityId)){
			$activity = $this->getProjectService()->getProjectActivityById($activityId);
			$this->edited = true;
		} else {
			$activity = new ProjectActivity();
		}
		
		$activity->setProjectId($projectId);
		$activity->setName($this->getValue('activityName'));
		$activity->setIsDeleted(ProjectActivity::ACTIVE_PROJECT);
		$activity->save();
		return $projectId;
	}

}

?>
