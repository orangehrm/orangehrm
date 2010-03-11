<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/doctrine/BaseFormFilterDoctrine.class.php');

/**
 * JobTitle filter form base class.
 *
 * @package    filters
 * @subpackage JobTitle *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class BaseJobTitleFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'          => new sfWidgetFormFilterInput(),
      'description'   => new sfWidgetFormFilterInput(),
      'comments'      => new sfWidgetFormFilterInput(),
      'salaryGradeId' => new sfWidgetFormFilterInput(),
      'jobspecId'     => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'name'          => new sfValidatorPass(array('required' => false)),
      'description'   => new sfValidatorPass(array('required' => false)),
      'comments'      => new sfValidatorPass(array('required' => false)),
      'salaryGradeId' => new sfValidatorPass(array('required' => false)),
      'jobspecId'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('job_title_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

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
      'salaryGradeId' => 'Text',
      'jobspecId'     => 'Number',
    );
  }
}