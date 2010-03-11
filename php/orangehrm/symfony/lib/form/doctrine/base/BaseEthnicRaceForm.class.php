<?php

/**
 * EthnicRace form base class.
 *
 * @package    form
 * @subpackage ethnic_race
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseEthnicRaceForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'ethnic_race_code' => new sfWidgetFormInputHidden(),
      'ethnic_race_desc' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'ethnic_race_code' => new sfValidatorDoctrineChoice(array('model' => 'EthnicRace', 'column' => 'ethnic_race_code', 'required' => false)),
      'ethnic_race_desc' => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ethnic_race[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'EthnicRace';
  }

}
