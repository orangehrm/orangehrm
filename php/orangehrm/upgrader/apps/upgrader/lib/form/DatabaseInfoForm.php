<?php

class DatabaseInfo extends sfForm {

    public function configure() {
        $this->setWidgets(array(
            'submitBy' => new sfWidgetFormInputHidden(array(), array('value' => 'databaseInfo')),
            'host' => new sfWidgetFormInputText(array(), array()),
            'port' => new sfWidgetFormInputText(array(), array()),
            'username' => new sfWidgetFormInputText(array(), array()),
            'password' => new sfWidgetFormInputPassword(array(), array()),
            'database_name' => new sfWidgetFormInputText(array(), array())
        ));
        
        $this->widgetSchema->setLabels(array(
            'host' => 'Host'.'<span class="required">*</span>',
            'port' => 'Port',
            'username' => 'Username'.'<span class="required">*</span>',
            'password' => 'Password',
            'database_name' => 'Database Name'.'<span class="required">*</span>'
        ));
        
        $this->widgetSchema->setNameFormat('databaseInfo[%s]');
        
        $this->setValidators(array(
            'submitBy' => new sfValidatorString(array('required' => true)),
            'host' => new sfValidatorString(array('required' => true), array('required' => 'Host is Empty')),
            'port' => new sfValidatorString(array('required' => false), array()),
            'username' => new sfValidatorString(array('required' => true), array('required' => 'Username is Empty')),
            'password' => new sfValidatorString(array('required' => false), array()),
            'database_name' => new sfValidatorString(array('required' => true), array('required' => 'Database Name is Empty')),
        ));
    }

}
