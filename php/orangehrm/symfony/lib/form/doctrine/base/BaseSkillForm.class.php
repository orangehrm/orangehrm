<?php

/**
 * Skill form base class.
 *
 * @package    form
 * @subpackage skill
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseSkillForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'skill_code'        => new sfWidgetFormInputHidden(),
      'skill_name'        => new sfWidgetFormInput(),
      'skill_description' => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'skill_code'        => new sfValidatorDoctrineChoice(array('model' => 'Skill', 'column' => 'skill_code', 'required' => false)),
      'skill_name'        => new sfValidatorString(array('max_length' => 120, 'required' => false)),
      'skill_description' => new sfValidatorString(array('max_length' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('skill[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Skill';
  }

}
