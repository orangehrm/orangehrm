<?php

class ResetPasswordForm extends sfForm {

    public $formWidgets = array();
    public $formValidators = array();

    public function configure() {
        $this->formWidgets['newPrimaryPassword'] = new sfWidgetFormInputPassword();
        $this->formWidgets['primaryPasswordConfirmation'] = new sfWidgetFormInputPassword();
        $this->formWidgets['newPrimaryPassword'] = new ohrmWidgetFormInputPassword(array(), array("autocomplete" => "off"));
        $this->formWidgets['primaryPasswordConfirmation'] = new sfWidgetFormInputPassword(array(), array("autocomplete" => "off"));
        $this->setWidgets($this->formWidgets);

        $this->formValidators['newPrimaryPassword'] = new sfValidatorString(array('required' => false));
        $this->formValidators['primaryPasswordConfirmation'] = new sfValidatorString(array('required' => false));
        $this->widgetSchema->setNameFormat('securityAuthentication[%s]');

        $this->setValidators($this->formValidators);
    }

}

