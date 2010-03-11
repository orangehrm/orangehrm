<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/doctrine/BaseFormFilterDoctrine.class.php');

/**
 * DefineKpi filter form base class.
 *
 * @package    filters
 * @subpackage DefineKpi *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class BaseDefineKpiFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'description'  => new sfWidgetFormFilterInput(),
      'minrate'      => new sfWidgetFormFilterInput(),
      'maxrate'      => new sfWidgetFormFilterInput(),
      'defaultscale' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'description'  => new sfValidatorPass(array('required' => false)),
      'minrate'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'maxrate'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'defaultscale' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('define_kpi_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

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
      'description'  => 'Text',
      'minrate'      => 'Number',
      'maxrate'      => 'Number',
      'defaultscale' => 'Number',
    );
  }
}