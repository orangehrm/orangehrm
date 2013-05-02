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

class SkillForm extends BaseForm {
    
    private $skillService;
    
    public function getSkillService() {
        
        if (!($this->skillService instanceof SkillService)) {
            $this->skillService = new SkillService();
        }
        
        return $this->skillService;
    }

    public function setSkillService($skillService) {
        $this->skillService = $skillService;
    }

    public function configure() {

        $this->setWidgets(array(
            'id' => new sfWidgetFormInputHidden(),
            'name' => new sfWidgetFormInputText(),
            'description' => new sfWidgetFormTextArea(array(),array('rows'=>5,'cols'=>10)),
        ));

        $this->setValidators(array(
            'id' => new sfValidatorNumber(array('required' => false)),
            'name' => new sfValidatorString(array('required' => true, 'max_length' => 120)),
            'description' => new sfValidatorString(array('required' => false, 'max_length' => 250)),
        ));

        $this->widgetSchema->setNameFormat('skill[%s]');

        $this->setDefault('id', '');
	}
    
    public function save() {
        
        $id = $this->getValue('id');
        
        if (empty($id)) {
            $skill = new Skill();
            $message = array('messageType' => 'success', 'message' => __(TopLevelMessages::SAVE_SUCCESS));
        } else {
            $skill = $this->getSkillService()->getSkillById($id);
            $message = array('messageType' => 'success', 'message' => __(TopLevelMessages::UPDATE_SUCCESS));
        }
        
        $skill->setName($this->getValue('name'));
        $skill->setDescription($this->getValue('description'));            
        $this->getSkillService()->saveSkill($skill);        
        
        return $message;
        
    }
    
    public function getSkillListAsJson() {

        $list = array();
        $skillList = $this->getSkillService()->getSkillList();
        foreach ($skillList as $skill) {
            $list[] = array('id' => $skill->getId(), 'name' => $skill->getName());
        }
        return json_encode($list);
    }

}

?>
