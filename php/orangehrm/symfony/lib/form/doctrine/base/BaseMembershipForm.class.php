<?php

/**
 * Membership form base class.
 *
 * @package    form
 * @subpackage membership
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseMembershipForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'membship_code' => new sfWidgetFormInputHidden(),
      'membtype_code' => new sfWidgetFormDoctrineChoice(array('model' => 'MembershipType', 'add_empty' => true)),
      'membship_name' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'membship_code' => new sfValidatorDoctrineChoice(array('model' => 'Membership', 'column' => 'membship_code', 'required' => false)),
      'membtype_code' => new sfValidatorDoctrineChoice(array('model' => 'MembershipType', 'required' => false)),
      'membship_name' => new sfValidatorString(array('max_length' => 120, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('membership[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Membership';
  }

}
