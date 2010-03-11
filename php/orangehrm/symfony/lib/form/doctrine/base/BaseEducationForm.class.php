<?php

/**
 * Education form base class.
 *
 * @package    form
 * @subpackage education
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseEducationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'eduCode' => new sfWidgetFormInputHidden(),
      'edu_uni' => new sfWidgetFormInput(),
      'edu_deg' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'eduCode' => new sfValidatorDoctrineChoice(array('model' => 'Education', 'column' => 'edu_code', 'required' => false)),
      'edu_uni' => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'edu_deg' => new sfValidatorString(array('max_length' => 100, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('education[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Education';
  }

}
