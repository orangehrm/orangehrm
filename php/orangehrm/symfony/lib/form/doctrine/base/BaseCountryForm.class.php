<?php

/**
 * Country form base class.
 *
 * @package    form
 * @subpackage country
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseCountryForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'cou_code' => new sfWidgetFormInputHidden(),
      'name'     => new sfWidgetFormInput(),
      'cou_name' => new sfWidgetFormInput(),
      'iso3'     => new sfWidgetFormInput(),
      'numcode'  => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'cou_code' => new sfValidatorDoctrineChoice(array('model' => 'Country', 'column' => 'cou_code', 'required' => false)),
      'name'     => new sfValidatorString(array('max_length' => 80)),
      'cou_name' => new sfValidatorString(array('max_length' => 80)),
      'iso3'     => new sfValidatorString(array('max_length' => 3, 'required' => false)),
      'numcode'  => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('country[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Country';
  }

}
