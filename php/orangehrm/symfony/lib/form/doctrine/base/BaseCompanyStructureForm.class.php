<?php

/**
 * CompanyStructure form base class.
 *
 * @method CompanyStructure getObject() Returns the current form's model object
 *
 * @package    orangehrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseCompanyStructureForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'title'       => new sfWidgetFormTextarea(),
      'description' => new sfWidgetFormTextarea(),
      'lft'         => new sfWidgetFormInputText(),
      'rgt'         => new sfWidgetFormInputText(),
      'id'          => new sfWidgetFormInputHidden(),
      'parnt'       => new sfWidgetFormInputText(),
      'loc_code'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('location'), 'add_empty' => true)),
      'dept_id'     => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'title'       => new sfValidatorString(array('max_length' => 2147483647)),
      'description' => new sfValidatorString(array('max_length' => 2147483647)),
      'lft'         => new sfValidatorInteger(array('required' => false)),
      'rgt'         => new sfValidatorInteger(array('required' => false)),
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'parnt'       => new sfValidatorInteger(array('required' => false)),
      'loc_code'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('location'), 'required' => false)),
      'dept_id'     => new sfValidatorString(array('max_length' => 32, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('company_structure[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CompanyStructure';
  }

}
