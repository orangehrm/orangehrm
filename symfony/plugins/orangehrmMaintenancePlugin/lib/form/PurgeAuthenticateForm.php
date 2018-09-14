<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 6/9/18
 * Time: 11:06 AM
 */
class PurgeAuthenticateForm extends sfForm{
    public function configure() {
        $this->setWidgets($this->getWidgetList());
        $this->getWidgetSchema()->setLabels($this->getLabelList());
        $this->setDefault('check_authenticate' ,'asadasdasd');
        $this->setValidators($this->getFormValidators());
    }
    private function getWidgetList() {
        $widgets = array();

        $widgets['confirm_password'] = new sfWidgetFormInputPassword();
        $widgets['check_authenticate'] = new sfWidgetFormInputHidden();
        return $widgets;
    }

    private function getLabelList() {
        $requiredMarker = ' <em>*</em>';
        $lableList = array();
        $lableList['confirm_password'] = __('Password') . $requiredMarker;
        return $lableList;
    }
    private function getFormValidators(){
        $validators = array();
        $validators['confirm_password'] = new sfValidatorString(array('required' => true));
        return $validators;
    }
}