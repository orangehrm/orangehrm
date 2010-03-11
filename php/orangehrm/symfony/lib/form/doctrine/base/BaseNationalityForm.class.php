<?php

/**
 * Nationality form base class.
 *
 * @package    form
 * @subpackage nationality
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseNationalityForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'nat_code' => new sfWidgetFormInputHidden(),
      'nat_name' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'nat_code' => new sfValidatorDoctrineChoice(array('model' => 'Nationality', 'column' => 'nat_code', 'required' => false)),
      'nat_name' => new sfValidatorString(array('max_length' => 120, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('nationality[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Nationality';
  }

}
