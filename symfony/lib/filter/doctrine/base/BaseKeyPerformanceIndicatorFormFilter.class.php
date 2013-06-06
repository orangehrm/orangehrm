<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/doctrine/BaseFormFilterDoctrine.class.php');

/**
 * KeyPerformanceIndicator filter form base class.
 *
 * @package    filters
 * @subpackage KeyPerformanceIndicator *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class BaseKeyPerformanceIndicatorFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'jobtitlecode' => new sfWidgetFormDoctrineChoice(array('model' => 'JobTitle', 'add_empty' => true)),
      'description'  => new sfWidgetFormFilterInput(),
      'minrate'      => new sfWidgetFormFilterInput(),
      'maxrate'      => new sfWidgetFormFilterInput(),
      'defaultscale' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'jobtitlecode' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'JobTitle', 'column' => 'id')),
      'description'  => new sfValidatorPass(array('required' => false)),
      'minrate'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'maxrate'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'defaultscale' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('key_performance_indicator_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'KeyPerformanceIndicator';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Text',
      'jobtitlecode' => 'ForeignKey',
      'description'  => 'Text',
      'minrate'      => 'Number',
      'maxrate'      => 'Number',
      'defaultscale' => 'Number',
    );
  }
}