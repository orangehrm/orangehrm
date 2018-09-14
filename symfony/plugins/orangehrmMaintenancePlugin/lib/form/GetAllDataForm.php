<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 7/9/18
 * Time: 12:00 PM
 */
class GetAllDataForm extends BaseForm
{
    public function configure()
    {
        $this->setWidgets($this->getWidgetList());
        $this->setValidators($this->getValidatorList());
        $this->getWidgetSchema()->setLabels($this->getLabelList());
    }

    public function getWidgetList()
    {
        $widgets = array();
        $widgets['employee'] = new ohrmWidgetEmployeeNameAutoFill();
        return $widgets;
    }

    public function getValidatorList()
    {
        $validators = array();
        $validators['employee'] = new ohrmValidatorEmployeeNameAutoFill(array('required' => true));
        return $validators;
    }

    public function getLabelList()
    {
        $requiredMarker = ' <em>*</em>';
        $lableList = array();
        $lableList['employee'] = __('Select Terminated Employee') . $requiredMarker;
        return $lableList;
    }
}