<?php

/**
 * EmployeeLanguage form base class.
 *
 * @package    form
 * @subpackage employee_language
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseEmployeeLanguageForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'emp_number' => new sfWidgetFormInputHidden(),
      'lang_code'  => new sfWidgetFormInputHidden(),
      'elang_type' => new sfWidgetFormInputHidden(),
      'competency' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'emp_number' => new sfValidatorDoctrineChoice(array('model' => 'EmployeeLanguage', 'column' => 'emp_number', 'required' => false)),
      'lang_code'  => new sfValidatorDoctrineChoice(array('model' => 'EmployeeLanguage', 'column' => 'lang_code', 'required' => false)),
      'elang_type' => new sfValidatorDoctrineChoice(array('model' => 'EmployeeLanguage', 'column' => 'elang_type', 'required' => false)),
      'competency' => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('employee_language[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EmployeeLanguage';
  }

}
