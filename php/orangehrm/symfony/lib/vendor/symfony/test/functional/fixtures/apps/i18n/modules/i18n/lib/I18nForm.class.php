<?php
class I18nForm extends sfForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'first_name' => new sfWidgetFormInputText(),
      'last_name'  => new sfWidgetFormInputText(),
      'email'      => new sfWidgetFormInputText(),
    ));
    
    $this->setValidators(array(
      'first_name' => new sfValidatorString(array('required' => true)),
      'last_name'  => new sfValidatorString(array('required' => true)),
      'email'      => new sfValidatorEmail(array('required' => true), 
                                           array('invalid' => '%value% is an invalid email address')),
    ));
    
    $this->widgetSchema->setLabel('email', 'Email address');
    $this->widgetSchema->setHelp('first_name', 'Put your first name here');
    
    $this->widgetSchema->setNameFormat('i18n[%s]');
  }
}
