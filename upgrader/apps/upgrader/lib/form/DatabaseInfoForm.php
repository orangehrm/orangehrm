<?php

class DatabaseInfo extends sfForm {

    public function configure() {
        sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
        $this->setWidgets(array(
            'submitBy' => new sfWidgetFormInputHidden(array(), array('value' => 'databaseInfo')),
            'host' => new sfWidgetFormInputText(array(), array()),
            'port' => new sfWidgetFormInputText(array(), array()),
            'username' => new sfWidgetFormInputText(array(), array()),
            'password' => new sfWidgetFormInputPassword(array(), array()),
            'database_name' => new sfWidgetFormInputText(array(), array())
        ));
        
        $this->widgetSchema->setLabels(array(
            'host' => __('Host').'<span class="required">*</span>',
            'port' => __('Port'),
            'username' => __('Username').'<span class="required">*</span>',
            'password' => __('Password'),
            'database_name' => __('Database Name').'<span class="required">*</span>'
        ));
        
        $this->widgetSchema->setNameFormat('databaseInfo[%s]');
        
        $this->setValidators(array(
            'submitBy' => new sfValidatorString(array('required' => true)),
            'host' => new sfValidatorString(array('required' => true), array('required' => __('Required'))),
            'port' => new sfValidatorString(array('required' => false), array()),
            'username' => new sfValidatorString(array('required' => true), array('required' => __('Required'))),
            'password' => new sfValidatorString(array('required' => false), array()),
            'database_name' => new sfValidatorString(array('required' => true), array('required' => __('Required'))),
        ));
    }

}
