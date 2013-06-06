<?php

/**
 * UniqueId form base class.
 *
 * @method UniqueId getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseUniqueIdForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'last_id'    => new sfWidgetFormInputText(),
      'dbTable'    => new sfWidgetFormInputText(),
      'field_name' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'last_id'    => new sfValidatorInteger(),
      'dbTable'    => new sfValidatorString(array('max_length' => 50)),
      'field_name' => new sfValidatorString(array('max_length' => 50)),
    ));

    $this->widgetSchema->setNameFormat('unique_id[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'UniqueId';
  }

}
