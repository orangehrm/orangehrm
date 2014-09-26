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

    protected $leaveTypePermissions;
    private $updateMode = false;
    private $leaveTypeService;

    public function configure() {
        $this->leaveTypePermissions = $this->getOption('leaveTypePermissions');
        $id = $this->getOption('leaveTypeId');

        sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');

        $widgets = array(
            'txtLeaveTypeName' => new sfWidgetFormInput(array(), array('size' => 30)),
            'excludeIfNoEntitlement' => new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1)),
            'hdnOriginalLeaveTypeName' => new sfWidgetFormInputHidden(),
            'hdnLeaveTypeId' => new sfWidgetFormInputHidden()
        );

        $validators = array(
            'txtLeaveTypeName' =>
            new sfValidatorString(array(
                'required' => true,
                'max_length' => 50
                    ),
                    array(
                        'required' => __('Required'),
                        'max_length' => __('Leave type name should be 50 characters or less in length')
            )),
            'excludeIfNoEntitlement' => new sfValidatorBoolean(),
            'hdnOriginalLeaveTypeName' => new sfValidatorString(array('required' => false)),
            'hdnLeaveTypeId' => new sfValidatorString(array('required' => false))
        );

        if (!(($this->leaveTypePermissions->canCreate() && empty($id)) || ($this->leaveTypePermissions->canUpdate() && $id > 0))) {
            foreach ($widgets as $widgetName => $widget) {
                $widget->setAttribute('disabled', 'disabled');
            }
        }

        $this->setWidgets($widgets);

        $this->getWidgetSchema()->setLabel('txtLeaveTypeName', __('Name') . ' <em>*</em>');
        $this->getWidgetSchema()->setLabel('excludeIfNoEntitlement', '<a id="exclude_link" href="#">' . __('Is entitlement situational') . '</a>');

        $this->setValidators($validators);
        $this->widgetSchema->setNameFormat('leaveType[%s]');
    }

    public function setDefaultValues($leaveTypeId) {

        $leaveTypeService = $this->getLeaveTypeService();
        $leaveTypeObject = $leaveTypeService->readLeaveType($leaveTypeId);

        if ($leaveTypeObject instanceof LeaveType) {

            $this->setDefault('hdnLeaveTypeId', $leaveTypeObject->getId());
            $this->setDefault('txtLeaveTypeName', $leaveTypeObject->getName());
            $this->setDefault('excludeIfNoEntitlement', $leaveTypeObject->getExcludeInReportsIfNoEntitlement());
            $this->setDefault('hdnOriginalLeaveTypeName', $leaveTypeObject->getName());
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
            $leaveType->setDeleted(0);
        }

        $leaveType->setName($this->getValue('txtLeaveTypeName'));
        $leaveType->setExcludeInReportsIfNoEntitlement($this->getValue('excludeIfNoEntitlement'));

        return $leaveType;
    }

    public function getDeletedLeaveTypesJsonArray() {

        $leaveTypeService = $this->getLeaveTypeService();
        $deletedLeaveTypes = $leaveTypeService->getDeletedLeaveTypeList();

        $deletedTypesArray = array();

        foreach ($deletedLeaveTypes as $deletedLeaveType) {
            $deletedTypesArray[] = array('id' => $deletedLeaveType->getId(),
                'name' => $deletedLeaveType->getName());
        }

        return json_encode($deletedTypesArray);
    }

    public function getLeaveTypeService() {

        if (is_null($this->leaveTypeService)) {
            $this->leaveTypeService = new LeaveTypeService();
        }

        return $this->leaveTypeService;
    }

    public function setLeaveTypeService($leaveTypeService) {
        $this->leaveTypeService = $leaveTypeService;
    }

    public function getJavaScripts() {
        $javaScripts = parent::getJavaScripts();
        $javaScripts[] = plugin_web_path('orangehrmLeavePlugin', 'js/defineLeaveTypeSuccess.js');

        return $javaScripts;
    }

    public function getStylesheets() {
        $styleSheets = parent::getStylesheets();
        return $styleSheets;
    }

    public function getActionButtons($leaveTypeId) {

        $actionButtons = array();
        if (($this->leaveTypePermissions->canCreate() && empty($leaveTypeId)) || ($this->leaveTypePermissions->canUpdate() && $leaveTypeId > 0 )) {
            $actionButtons['saveButton'] = new ohrmWidgetButton('saveButton', "Save", array());
        }
        $actionButtons['backButton'] = new ohrmWidgetButton('backButton', "Cancel", array('class' => 'cancel'));

        return $actionButtons;
    }

}

