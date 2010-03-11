<?php

/**
 * Licenses form base class.
 *
 * @package    form
 * @subpackage licenses
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseLicensesForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'licenses_code' => new sfWidgetFormInputHidden(),
      'licenses_desc' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'licenses_code' => new sfValidatorDoctrineChoice(array('model' => 'Licenses', 'column' => 'licenses_code', 'required' => false)),
      'licenses_desc' => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('licenses[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Licenses';
  }

}
