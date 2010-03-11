<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/doctrine/BaseFormFilterDoctrine.class.php');

/**
 * PerformanceReview filter form base class.
 *
 * @package    filters
 * @subpackage PerformanceReview *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class BasePerformanceReviewFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'reviewerid'       => new sfWidgetFormFilterInput(),
      'reviewperiodfrom' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'reviewperiodto'   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'reviewdate'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'duedate'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'reviewcomment'    => new sfWidgetFormFilterInput(),
      'reviewstate'      => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'reviewerid'       => new sfValidatorPass(array('required' => false)),
      'reviewperiodfrom' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'reviewperiodto'   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'reviewdate'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'duedate'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'reviewcomment'    => new sfValidatorPass(array('required' => false)),
      'reviewstate'      => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('performance_review_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'PerformanceReview';
  }

  public function getFields()
  {
    return array(
      'id'               => 'Text',
      'reviewerid'       => 'Text',
      'reviewperiodfrom' => 'Date',
      'reviewperiodto'   => 'Date',
      'reviewdate'       => 'Date',
      'duedate'          => 'Date',
      'reviewcomment'    => 'Text',
      'reviewstate'      => 'Text',
    );
  }
}