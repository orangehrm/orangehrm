<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/doctrine/BaseFormFilterDoctrine.class.php');

/**
 * CompanyStructure filter form base class.
 *
 * @package    filters
 * @subpackage CompanyStructure *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class BaseCompanyStructureFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'title'       => new sfWidgetFormFilterInput(),
      'description' => new sfWidgetFormFilterInput(),
      'lft'         => new sfWidgetFormFilterInput(),
      'rgt'         => new sfWidgetFormFilterInput(),
      'parnt'       => new sfWidgetFormFilterInput(),
      'loc_code'    => new sfWidgetFormFilterInput(),
      'dept_id'     => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'title'       => new sfValidatorPass(array('required' => false)),
      'description' => new sfValidatorPass(array('required' => false)),
      'lft'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'rgt'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'parnt'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'loc_code'    => new sfValidatorPass(array('required' => false)),
      'dept_id'     => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('company_structure_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

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
      'loc_code'    => 'Text',
      'dept_id'     => 'Text',
    );
  }
}