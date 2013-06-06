<?php

/**
 * EthnicRace form base class.
 *
 * @method EthnicRace getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseEthnicRaceForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'ethnic_race_code' => new sfWidgetFormInputHidden(),
      'ethnic_race_desc' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'ethnic_race_code' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('ethnic_race_code')), 'empty_value' => $this->getObject()->get('ethnic_race_code'), 'required' => false)),
      'ethnic_race_desc' => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ethnic_race[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'EthnicRace';
  }

}
