<?php

/**
 * Language form base class.
 *
 * @package    form
 * @subpackage language
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseLanguageForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'lang_code' => new sfWidgetFormInputHidden(),
      'lang_name' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'lang_code' => new sfValidatorDoctrineChoice(array('model' => 'Language', 'column' => 'lang_code', 'required' => false)),
      'lang_name' => new sfValidatorString(array('max_length' => 120, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('language[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Language';
  }

}
