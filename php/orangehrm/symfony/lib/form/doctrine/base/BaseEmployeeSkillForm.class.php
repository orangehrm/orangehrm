<?php

/**
 * EmployeeSkill form base class.
 *
 * @package    form
 * @subpackage employee_skill
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseEmployeeSkillForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'emp_number'   => new sfWidgetFormDoctrineChoice(array('model' => 'Employee', 'add_empty' => false)),
      'skill_code'   => new sfWidgetFormDoctrineChoice(array('model' => 'Skill', 'add_empty' => false)),
      'years_of_exp' => new sfWidgetFormInput(),
      'comments'     => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorDoctrineChoice(array('model' => 'EmployeeSkill', 'column' => 'id', 'required' => false)),
      'emp_number'   => new sfValidatorDoctrineChoice(array('model' => 'Employee')),
      'skill_code'   => new sfValidatorDoctrineChoice(array('model' => 'Skill')),
      'years_of_exp' => new sfValidatorNumber(),
      'comments'     => new sfValidatorString(array('max_length' => 100)),
    ));

    $this->widgetSchema->setNameFormat('employee_skill[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EmployeeSkill';
  }

}
