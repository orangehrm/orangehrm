<?php

/**
 * MembershipType form base class.
 *
 * @package    form
 * @subpackage membership_type
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseMembershipTypeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'membtype_code' => new sfWidgetFormInputHidden(),
      'membtype_name' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'membtype_code' => new sfValidatorDoctrineChoice(array('model' => 'MembershipType', 'column' => 'membtype_code', 'required' => false)),
      'membtype_name' => new sfValidatorString(array('max_length' => 120, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('membership_type[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'MembershipType';
  }

}
