<?php

/**
 * EmployeeEducation form base class.
 *
 * @package    form
 * @subpackage employee_education
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseEmployeeEducationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'empNumber'      => new sfWidgetFormInputHidden(),
      'eduCode'        => new sfWidgetFormInputHidden(),
      'edu_major'      => new sfWidgetFormInput(),
      'edu_year'       => new sfWidgetFormInput(),
      'edu_gpa'        => new sfWidgetFormInput(),
      'edu_start_date' => new sfWidgetFormDateTime(),
      'edu_end_date'   => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'empNumber'      => new sfValidatorDoctrineChoice(array('model' => 'EmployeeEducation', 'column' => 'emp_number', 'required' => false)),
      'eduCode'        => new sfValidatorDoctrineChoice(array('model' => 'EmployeeEducation', 'column' => 'edu_code', 'required' => false)),
      'edu_major'      => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'edu_year'       => new sfValidatorNumber(array('required' => false)),
      'edu_gpa'        => new sfValidatorString(array('max_length' => 25, 'required' => false)),
      'edu_start_date' => new sfValidatorDateTime(array('required' => false)),
      'edu_end_date'   => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('employee_education[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EmployeeEducation';
  }

}
