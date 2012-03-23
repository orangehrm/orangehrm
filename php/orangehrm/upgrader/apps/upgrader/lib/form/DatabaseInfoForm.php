<?php

class DatabaseInfo extends sfForm {

    public function configure() {
        $this->setWidgets(array(
            'submitBy' => new sfWidgetFormInputHidden(array(), array('value' => 'databaseInfo')),
            'host' => new sfWidgetFormInputText(array(), array()),
            'port' => new sfWidgetFormInputText(array(), array()),
            'user' => new sfWidgetFormInputText(array(), array()),
            'password' => new sfWidgetFormInputPassword(array(), array()),
            'database_name' => new sfWidgetFormInputText(array(), array())
        ));
        
        $this->widgetSchema->setLabels(array(
            'host' => 'Host',
            'port' => 'Port',
            'user' => 'user',
            'password' => 'Password',
            'database_name' => 'Database Name'
        ));
        
        $this->widgetSchema->setNameFormat('databaseInfo[%s]');
        
        $this->setValidators(array(
            'submitBy' => new sfValidatorString(array('required' => true)),
            'host' => new sfValidatorString(array('required' => true), array('required' => 'Host is Empty')),
            'port' => new sfValidatorString(array('required' => false), array()),
            'user' => new sfValidatorString(array('required' => true), array('required' => 'User is Empty')),
            'password' => new sfValidatorString(array('required' => false), array()),
            'database_name' => new sfValidatorString(array('required' => true), array('required' => 'Database Name is Empty')),
        ));
    }

}
