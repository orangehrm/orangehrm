<?php

/**
 * EmployeeEducation form base class.
 *
 * @method EmployeeEducation getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseEmployeeEducationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'emp_number' => new sfWidgetFormInputHidden(),
      'code'       => new sfWidgetFormInputHidden(),
      'major'      => new sfWidgetFormInputText(),
      'year'       => new sfWidgetFormInputText(),
      'gpa'        => new sfWidgetFormInputText(),
      'start_date' => new sfWidgetFormDateTime(),
      'end_date'   => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'emp_number' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('emp_number')), 'empty_value' => $this->getObject()->get('emp_number'), 'required' => false)),
      'code'       => new sfValidatorChoice(array('choices' => array($this->getObject()->get('code')), 'empty_value' => $this->getObject()->get('code'), 'required' => false)),
      'major'      => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'year'       => new sfValidatorNumber(array('required' => false)),
      'gpa'        => new sfValidatorString(array('max_length' => 25, 'required' => false)),
      'start_date' => new sfValidatorDateTime(array('required' => false)),
      'end_date'   => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('employee_education[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'EmployeeEducation';
  }

}
