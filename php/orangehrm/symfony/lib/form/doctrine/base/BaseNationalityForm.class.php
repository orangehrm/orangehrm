<?php

/**
 * Nationality form base class.
 *
 * @method Nationality getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseNationalityForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'nat_code' => new sfWidgetFormInputHidden(),
      'nat_name' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'nat_code' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('nat_code')), 'empty_value' => $this->getObject()->get('nat_code'), 'required' => false)),
      'nat_name' => new sfValidatorString(array('max_length' => 120, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('nationality[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Nationality';
  }

}
