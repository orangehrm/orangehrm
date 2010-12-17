<?php

/**
 * CompanyStructure filter form base class.
 *
 * @package    orangehrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseCompanyStructureFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'title'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'description' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'lft'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'rgt'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'parnt'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'loc_code'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('location'), 'add_empty' => true)),
      'dept_id'     => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'title'       => new sfValidatorPass(array('required' => false)),
      'description' => new sfValidatorPass(array('required' => false)),
      'lft'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'rgt'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'parnt'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'loc_code'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('location'), 'column' => 'loc_code')),
      'dept_id'     => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('company_structure_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CompanyStructure';
  }

  public function getFields()
  {
    return array(
      'title'       => 'Text',
      'description' => 'Text',
      'lft'         => 'Number',
      'rgt'         => 'Number',
      'id'          => 'Number',
      'parnt'       => 'Number',
      'loc_code'    => 'ForeignKey',
      'dept_id'     => 'Text',
    );
  }
}
