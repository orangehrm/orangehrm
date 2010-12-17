<?php

/**
 * Language form base class.
 *
 * @method Language getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseLanguageForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'lang_code' => new sfWidgetFormInputHidden(),
      'lang_name' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'lang_code' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('lang_code')), 'empty_value' => $this->getObject()->get('lang_code'), 'required' => false)),
      'lang_name' => new sfValidatorString(array('max_length' => 120, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('language[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Language';
  }

}
