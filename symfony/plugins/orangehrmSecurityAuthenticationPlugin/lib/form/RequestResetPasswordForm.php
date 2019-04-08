<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RequestResetPasswordForm
 *
 * @author orangehrm
 */
class RequestResetPasswordForm extends sfForm {

    public $formWidgets = array();
    public $formValidators = array();

    public function configure() {
        $this->formWidgets['userName'] = new sfWidgetFormInputText();

        $this->setWidgets($this->formWidgets);

        $this->formValidators['userName'] = new sfValidatorString(
            array('required' => false),
            array('invalid' => __('The username is invalid.')
            ));
        $this->widgetSchema->setNameFormat('securityAuthentication[%s]');

        $this->setValidators($this->formValidators);
    }

}
