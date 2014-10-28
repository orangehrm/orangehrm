<?php

/**
 * PerformanceReview filter form base class.
 *
 * @package    orangehrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasePerformanceReviewFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'employeeId'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Employee'), 'add_empty' => true)),
      'reviewerId'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Reviewer'), 'add_empty' => true)),
      'creatorId'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Creator'), 'add_empty' => true)),
      'jobTitleCode'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('JobTitle'), 'add_empty' => true)),
      'subDivisionId' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('SubDivision'), 'add_empty' => true)),
      'creationDate'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'periodFrom'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'periodTo'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'dueDate'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'state'         => new sfWidgetFormFilterInput(),
      'kpis'          => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'employeeId'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Employee'), 'column' => 'empNumber')),
      'reviewerId'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Reviewer'), 'column' => 'empNumber')),
      'creatorId'     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Creator'), 'column' => 'id')),
      'jobTitleCode'  => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('JobTitle'), 'column' => 'id')),
      'subDivisionId' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('SubDivision'), 'column' => 'id')),
      'creationDate'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'periodFrom'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'periodTo'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'dueDate'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'state'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'kpis'          => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('performance_review_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PerformanceReview';
  }

  public function getFields()
  {
    return array(
      'id'            => 'Number',
      'employeeId'    => 'ForeignKey',
      'reviewerId'    => 'ForeignKey',
      'creatorId'     => 'ForeignKey',
      'jobTitleCode'  => 'ForeignKey',
      'subDivisionId' => 'ForeignKey',
      'creationDate'  => 'Date',
      'periodFrom'    => 'Date',
      'periodTo'      => 'Date',
      'dueDate'       => 'Date',
      'state'         => 'Number',
      'kpis'          => 'Text',
    );
  }
}
