<?php

/**
 * CustomFields form base class.
 *
 * @method CustomFields getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseCustomFieldsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'field_num'  => new sfWidgetFormInputHidden(),
      'name'       => new sfWidgetFormInputText(),
      'type'       => new sfWidgetFormInputText(),
      'extra_data' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'field_num'  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('field_num')), 'empty_value' => $this->getObject()->get('field_num'), 'required' => false)),
      'name'       => new sfValidatorString(array('max_length' => 250)),
      'type'       => new sfValidatorInteger(),
      'extra_data' => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('custom_fields[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CustomFields';
  }

}
