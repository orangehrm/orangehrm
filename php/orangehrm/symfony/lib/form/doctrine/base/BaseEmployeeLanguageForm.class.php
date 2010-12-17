<?php

/**
 * EmployeeLanguage form base class.
 *
 * @method EmployeeLanguage getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseEmployeeLanguageForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'emp_number' => new sfWidgetFormInputHidden(),
      'code'       => new sfWidgetFormInputHidden(),
      'lang_type'  => new sfWidgetFormInputHidden(),
      'competency' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'emp_number' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('emp_number')), 'empty_value' => $this->getObject()->get('emp_number'), 'required' => false)),
      'code'       => new sfValidatorChoice(array('choices' => array($this->getObject()->get('code')), 'empty_value' => $this->getObject()->get('code'), 'required' => false)),
      'lang_type'  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('lang_type')), 'empty_value' => $this->getObject()->get('lang_type'), 'required' => false)),
      'competency' => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('employee_language[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'EmployeeLanguage';
  }

}
