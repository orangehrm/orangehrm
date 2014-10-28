<?php

/**
 * Country form base class.
 *
 * @method Country getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseCountryForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'cou_code' => new sfWidgetFormInputHidden(),
      'name'     => new sfWidgetFormInputText(),
      'cou_name' => new sfWidgetFormInputText(),
      'iso3'     => new sfWidgetFormInputText(),
      'numcode'  => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'cou_code' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('cou_code')), 'empty_value' => $this->getObject()->get('cou_code'), 'required' => false)),
      'name'     => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'cou_name' => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'iso3'     => new sfValidatorString(array('max_length' => 3, 'required' => false)),
      'numcode'  => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('country[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Country';
  }

}
