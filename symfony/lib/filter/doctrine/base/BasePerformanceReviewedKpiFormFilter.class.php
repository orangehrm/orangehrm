<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/doctrine/BaseFormFilterDoctrine.class.php');

/**
 * PerformanceReviewedKpi filter form base class.
 *
 * @package    filters
 * @subpackage PerformanceReviewedKpi *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class BasePerformanceReviewedKpiFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormFilterInput(),
      'id'           => new sfWidgetFormFilterInput(),
      'jobtitlecode' => new sfWidgetFormFilterInput(),
      'kpiid'        => new sfWidgetFormFilterInput(),
      'rating'       => new sfWidgetFormFilterInput(),
      'comment'      => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorPass(array('required' => false)),
      'id'           => new sfValidatorPass(array('required' => false)),
      'jobtitlecode' => new sfValidatorPass(array('required' => false)),
      'kpiid'        => new sfValidatorPass(array('required' => false)),
      'rating'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'comment'      => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('performance_reviewed_kpi_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'PerformanceReviewedKpi';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Text',
      'id'           => 'Text',
      'jobtitlecode' => 'Text',
      'kpiid'        => 'Text',
      'rating'       => 'Number',
      'comment'      => 'Text',
    );
  }
}