<?php

/**
 * Licenses form base class.
 *
 * @method Licenses getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseLicensesForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'licenses_code' => new sfWidgetFormInputHidden(),
      'licenses_desc' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'licenses_code' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('licenses_code')), 'empty_value' => $this->getObject()->get('licenses_code'), 'required' => false)),
      'licenses_desc' => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('licenses[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Licenses';
  }

}
