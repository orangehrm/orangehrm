<?php

/**
 * UniqueId form base class.
 *
 * @package    form
 * @subpackage unique_id
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseUniqueIdForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'last_id'    => new sfWidgetFormInput(),
      'table_name' => new sfWidgetFormInput(),
      'field_name' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorDoctrineChoice(array('model' => 'UniqueId', 'column' => 'id', 'required' => false)),
      'last_id'    => new sfValidatorInteger(),
      'table_name' => new sfValidatorString(array('max_length' => 50)),
      'field_name' => new sfValidatorString(array('max_length' => 50)),
    ));

    $this->widgetSchema->setNameFormat('unique_id[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'UniqueId';
  }

}
