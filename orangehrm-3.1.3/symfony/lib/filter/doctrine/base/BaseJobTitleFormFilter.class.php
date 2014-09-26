<?php

/**
 * JobTitle filter form base class.
 *
 * @package    orangehrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseJobTitleFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'          => new sfWidgetFormFilterInput(),
      'description'   => new sfWidgetFormFilterInput(),
      'comments'      => new sfWidgetFormFilterInput(),
      'salaryGradeId' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('SalaryGrade'), 'add_empty' => true)),
      'jobspecId'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('JobSpecifications'), 'add_empty' => true)),
      'isActive'      => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'name'          => new sfValidatorPass(array('required' => false)),
      'description'   => new sfValidatorPass(array('required' => false)),
      'comments'      => new sfValidatorPass(array('required' => false)),
      'salaryGradeId' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('SalaryGrade'), 'column' => 'sal_grd_code')),
      'jobspecId'     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('JobSpecifications'), 'column' => 'jobspec_id')),
      'isActive'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('job_title_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'JobTitle';
  }

  public function getFields()
  {
    return array(
      'id'            => 'Text',
      'name'          => 'Text',
      'description'   => 'Text',
      'comments'      => 'Text',
      'salaryGradeId' => 'ForeignKey',
      'jobspecId'     => 'ForeignKey',
      'isActive'      => 'Number',
    );
  }
}
