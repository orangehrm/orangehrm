<?php

/**
 * Skill form base class.
 *
 * @method Skill getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSkillForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'skill_code'        => new sfWidgetFormInputHidden(),
      'skill_name'        => new sfWidgetFormInputText(),
      'skill_description' => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'skill_code'        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('skill_code')), 'empty_value' => $this->getObject()->get('skill_code'), 'required' => false)),
      'skill_name'        => new sfValidatorString(array('max_length' => 120, 'required' => false)),
      'skill_description' => new sfValidatorString(array('max_length' => 2147483647, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('skill[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Skill';
  }

}
