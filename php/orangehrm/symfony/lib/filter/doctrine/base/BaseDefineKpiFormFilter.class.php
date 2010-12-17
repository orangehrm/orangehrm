<?php

/**
 * DefineKpi filter form base class.
 *
 * @package    orangehrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseDefineKpiFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'jobtitlecode' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('JobTitle'), 'add_empty' => true)),
      'desc'         => new sfWidgetFormFilterInput(),
      'min'          => new sfWidgetFormFilterInput(),
      'max'          => new sfWidgetFormFilterInput(),
      'default'      => new sfWidgetFormFilterInput(),
      'isactive'     => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'jobtitlecode' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('JobTitle'), 'column' => 'id')),
      'desc'         => new sfValidatorPass(array('required' => false)),
      'min'          => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'max'          => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'default'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'isactive'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('define_kpi_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DefineKpi';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'jobtitlecode' => 'ForeignKey',
      'desc'         => 'Text',
      'min'          => 'Number',
      'max'          => 'Number',
      'default'      => 'Number',
      'isactive'     => 'Number',
    );
  }
}
