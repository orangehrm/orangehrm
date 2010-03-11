<?php

/**
 * CustomFields form base class.
 *
 * @package    form
 * @subpackage custom_fields
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseCustomFieldsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'field_num'  => new sfWidgetFormInputHidden(),
      'name'       => new sfWidgetFormInput(),
      'type'       => new sfWidgetFormInput(),
      'extra_data' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'field_num'  => new sfValidatorDoctrineChoice(array('model' => 'CustomFields', 'column' => 'field_num', 'required' => false)),
      'name'       => new sfValidatorString(array('max_length' => 250)),
      'type'       => new sfValidatorInteger(),
      'extra_data' => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('custom_fields[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CustomFields';
  }

}
