<?php

/**
 * Education form base class.
 *
 * @method Education getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseEducationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'eduCode' => new sfWidgetFormInputHidden(),
      'edu_uni' => new sfWidgetFormInputText(),
      'edu_deg' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'eduCode' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('eduCode')), 'empty_value' => $this->getObject()->get('eduCode'), 'required' => false)),
      'edu_uni' => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'edu_deg' => new sfValidatorString(array('max_length' => 100, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('education[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Education';
  }

}
