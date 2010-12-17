<?php

/**
 * MembershipType form base class.
 *
 * @method MembershipType getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseMembershipTypeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'membtype_code' => new sfWidgetFormInputHidden(),
      'membtype_name' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'membtype_code' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('membtype_code')), 'empty_value' => $this->getObject()->get('membtype_code'), 'required' => false)),
      'membtype_name' => new sfValidatorString(array('max_length' => 120, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('membership_type[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'MembershipType';
  }

}
