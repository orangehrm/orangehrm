<?php

/**
 * Membership form base class.
 *
 * @method Membership getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseMembershipForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'membship_code' => new sfWidgetFormInputHidden(),
      'membtype_code' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('MembershipType'), 'add_empty' => true)),
      'membship_name' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'membship_code' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('membship_code')), 'empty_value' => $this->getObject()->get('membship_code'), 'required' => false)),
      'membtype_code' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('MembershipType'), 'required' => false)),
      'membship_name' => new sfValidatorString(array('max_length' => 120, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('membership[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Membership';
  }

}
