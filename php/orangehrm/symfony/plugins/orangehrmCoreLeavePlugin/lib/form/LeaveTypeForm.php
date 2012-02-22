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
 */

class LeaveTypeForm extends orangehrmForm {

    private $updateMode = false;
    private $leaveTypeService;

    public function configure() {

        sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
        
        $this->setWidgets(array(
            'txtLeaveTypeName' => new sfWidgetFormInput(array(), array('size' => 30)),
            'hdnOriginalLeaveTypeName' => new sfWidgetFormInputHidden(),
            'hdnLeaveTypeId' => new sfWidgetFormInputHidden()
        ));
        
        $this->setValidators(array(
            'txtLeaveTypeName' => 
                new sfValidatorString(array(
                        'required' => true,
                        'max_length' => 50
                    ),
                    array(
                        'required' => __('Required'),
                        'max_length' => __('Leave type name should be 50 characters or less in length')
                    )),
            'hdnOriginalLeaveTypeName' => new sfValidatorString(array('required' => false)),
            'hdnLeaveTypeId' => new sfValidatorString(array('required' => false))          
        ));
        $this->widgetSchema->setNameFormat('leaveType[%s]');
    }

    public function setDefaultValues($leaveTypeId) {

        $leaveTypeService = $this->getLeaveTypeService();
        $leaveTypeObject = $leaveTypeService->readLeaveType($leaveTypeId);

        if ($leaveTypeObject instanceof LeaveType) {

            $this->setDefault('hdnLeaveTypeId', $leaveTypeObject->getLeaveTypeId());
            $this->setDefault('txtLeaveTypeName', $leaveTypeObject->getLeaveTypeName());
            $this->setDefault('hdnOriginalLeaveTypeName', $leaveTypeObject->getLeaveTypeName());
        }
    }

    public function setUpdateMode() {
        $this->updateMode = true;
    }    

    public function isUpdateMode() {
        return $this->updateMode;
    }
    
    public function getLeaveTypeObject() {
        
        $leaveTypeId = $this->getValue('hdnLeaveTypeId');
        
        if (!empty($leaveTypeId)) {
            $leaveType = $this->getLeaveTypeService()->readLeaveType($leaveTypeId);
        } else {
            $leaveType = new LeaveType();
            $leaveType->setAvailableFlag(LeaveType::AVAILABLE);
        }        
        
        $leaveType->setLeaveTypeName($this->getValue('txtLeaveTypeName'));

        return $leaveType;        
    }
    
    public function getDeletedLeaveTypesJsonArray() {

        $leaveTypeService = $this->getLeaveTypeService();
        $deletedLeaveTypes = $leaveTypeService->getDeletedLeaveTypeList();

        $deletedTypesArray = array();

        foreach ($deletedLeaveTypes as $deletedLeaveType) {
            $deletedTypesArray[] = array('id' => $deletedLeaveType->getLeaveTypeId(),
                                         'name' => $deletedLeaveType->getLeaveTypeName());
        }

        return json_encode($deletedTypesArray);
    }

    public function getLeaveTypeService() {

        if(is_null($this->leaveTypeService)) {
            $this->leaveTypeService = new LeaveTypeService();
        }

        return $this->leaveTypeService;

    }    
    
    public function setLeaveTypeService($leaveTypeService) {
        $this->leaveTypeService = $leaveTypeService;
    }
    
    public function getJavaScripts() {
        $javaScripts = parent::getJavaScripts();
        $javaScripts[] = '/orangehrmCoreLeavePlugin/js/defineLeaveTypeSuccess.js';
        
        return $javaScripts;
    }
    
    public function getStylesheets() {
        $styleSheets = parent::getStylesheets();
        $styleSheets['/orangehrmCoreLeavePlugin/css/defineLeaveTypeSuccess.css'] = 'screen';
        
        return $styleSheets;        
    }
    
    public function getActionButtons() {

        $actionButtons = array();
        
        $actionButtons['saveButton'] = new ohrmWidgetButton('saveButton', "Save", array('class' => 'savebutton'));
        $actionButtons['resetButton'] = new ohrmWidgetButton('resetButton', "Reset", array('class' => 'savebutton', 'type'=> 'reset'));
        $actionButtons['backButton'] = new ohrmWidgetButton('backButton', "Back", array('class' => 'savebutton'));

        return $actionButtons;
    }    
}

