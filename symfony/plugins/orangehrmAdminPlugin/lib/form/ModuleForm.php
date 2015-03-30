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

class ModuleForm extends BaseForm {
    
    private $moduleService;
    
    public function getModuleService() {
        
        if (!($this->moduleService instanceof ModuleService)) {
            $this->moduleService = new ModuleService();
        }
        
        return $this->moduleService;
    }

    public function setModuleService($moduleService) {
        $this->moduleService = $moduleService;
    }

    public function configure() {

        $this->setWidgets(array(
            'admin' => new sfWidgetFormInputCheckbox(array(), array('class' => 'checkbox', 'value' => 'on')),
            'pim' => new sfWidgetFormInputCheckbox(array(), array('class' => 'checkbox', 'value' => 'on')),
            'leave' => new sfWidgetFormInputCheckbox(array(), array('class' => 'checkbox', 'value' => 'on')),
            'time' => new sfWidgetFormInputCheckbox(array(), array('class' => 'checkbox', 'value' => 'on')),
            'recruitment' => new sfWidgetFormInputCheckbox(array(), array('class' => 'checkbox', 'value' => 'on')),
            'performance' => new sfWidgetFormInputCheckbox(array(), array('class' => 'checkbox', 'value' => 'on')),
            'help' => new sfWidgetFormInputCheckbox(array(), array('class' => 'checkbox', 'value' => 'on')),
            'directory' => new sfWidgetFormInputCheckbox(array(), array('class' => 'checkbox', 'value' => 'on')),
        ));        
        
        $this->setValidators(array(
            'admin' => new sfValidatorPass(),
            'pim' => new sfValidatorPass(),
            'leave' => new sfValidatorPass(),
            'time' => new sfValidatorPass(),
            'recruitment' => new sfValidatorPass(),
            'performance' => new sfValidatorPass(),
            'help' => new sfValidatorPass(),
            'directory' => new sfValidatorPass()
        ));
        
        $this->setDefaults($this->_getDefaultValues());
        $this->setDefault('help', true);

        $this->widgetSchema->setNameFormat('moduleConfig[%s]');

	}
    
    protected function _getDefaultValues() {
        
        $modules = array('admin', 'pim', 'leave', 'time', 'recruitment', 'performance', 'directory');
        
        $moduleService = $this->getModuleService();
        $disabledModules = $moduleService->getDisabledModuleList();
        $disabledModuleList = array();
        
        foreach ($disabledModules as $module) {
            $disabledModuleList[] = $module->getName();
        }
        
        $modules = array_diff($modules, $disabledModuleList);
        
        $defaultValues = array();
        
        foreach ($modules as $module) {
            $defaultValues[$module] = true;
        }
        
        return $defaultValues;
        
    }


    public function save() {
        
        $modules = $this->getValues();
        
        $modulesToEnable = array();
        $modulesToDisable = array();
        $defaultModules = array('admin', 'pim');
        
        foreach ($modules as $key => $value) {
            
            if (!empty($value)) {
                
                $modulesToEnable[] = $key;
                
                if ($key == 'time') {
                    $modulesToEnable[] = 'attendance';
                }
                
                if ($key == 'recruitment') {
                    $modulesToEnable[] = 'recruitmentApply';
                }                
                
            } else {
                
                if (!in_array($key, $defaultModules)) {
                
                    $modulesToDisable[] = $key;

                    if ($key == 'time') {
                        $modulesToDisable[] = 'attendance';
                    }

                    if ($key == 'recruitment') {
                        $modulesToDisable[] = 'recruitmentApply';
                    }
                
                }
                
            }
            
        }
        
        if (!empty($modulesToEnable)) {
            $this->getModuleService()->updateModuleStatus($modulesToEnable, Module::ENABLED);
        }
        
        if (!empty($modulesToDisable)) {
            $this->getModuleService()->updateModuleStatus($modulesToDisable, Module::DISABLED);
        }
        
        return array('messageType' => 'success', 'message' => __(TopLevelMessages::SAVE_SUCCESS));
        
    }

}

?>
