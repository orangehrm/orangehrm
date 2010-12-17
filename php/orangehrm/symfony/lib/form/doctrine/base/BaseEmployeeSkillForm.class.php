<?php

/**
 * EmployeeSkill form base class.
 *
 * @method EmployeeSkill getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseEmployeeSkillForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'emp_number'   => new sfWidgetFormInputHidden(),
      'code'         => new sfWidgetFormInputHidden(),
      'years_of_exp' => new sfWidgetFormInputText(),
      'comments'     => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'emp_number'   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('emp_number')), 'empty_value' => $this->getObject()->get('emp_number'), 'required' => false)),
      'code'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('code')), 'empty_value' => $this->getObject()->get('code'), 'required' => false)),
      'years_of_exp' => new sfValidatorNumber(array('required' => false)),
      'comments'     => new sfValidatorString(array('max_length' => 100, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('employee_skill[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'EmployeeSkill';
  }

}
