<?php

/**
 * Province form base class.
 *
 * @package    form
 * @subpackage province
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseProvinceForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'province_name' => new sfWidgetFormInput(),
      'province_code' => new sfWidgetFormInput(),
      'cou_code'      => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorDoctrineChoice(array('model' => 'Province', 'column' => 'id', 'required' => false)),
      'province_name' => new sfValidatorString(array('max_length' => 40)),
      'province_code' => new sfValidatorString(array('max_length' => 2)),
      'cou_code'      => new sfValidatorString(array('max_length' => 2)),
    ));

    $this->widgetSchema->setNameFormat('province[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Province';
  }

}
