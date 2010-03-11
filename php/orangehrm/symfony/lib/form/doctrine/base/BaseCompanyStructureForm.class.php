<?php

/**
 * CompanyStructure form base class.
 *
 * @package    form
 * @subpackage company_structure
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseCompanyStructureForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'title'       => new sfWidgetFormTextarea(),
      'description' => new sfWidgetFormTextarea(),
      'lft'         => new sfWidgetFormInput(),
      'rgt'         => new sfWidgetFormInput(),
      'id'          => new sfWidgetFormInputHidden(),
      'parnt'       => new sfWidgetFormInput(),
      'loc_code'    => new sfWidgetFormDoctrineChoice(array('model' => 'Location', 'add_empty' => true)),
      'dept_id'     => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'title'       => new sfValidatorString(array('max_length' => 2147483647)),
      'description' => new sfValidatorString(array('max_length' => 2147483647)),
      'lft'         => new sfValidatorInteger(),
      'rgt'         => new sfValidatorInteger(),
      'id'          => new sfValidatorDoctrineChoice(array('model' => 'CompanyStructure', 'column' => 'id', 'required' => false)),
      'parnt'       => new sfValidatorInteger(),
      'loc_code'    => new sfValidatorDoctrineChoice(array('model' => 'Location', 'required' => false)),
      'dept_id'     => new sfValidatorString(array('max_length' => 32, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('company_structure[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CompanyStructure';
  }

}
