<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/doctrine/BaseFormFilterDoctrine.class.php');

/**
 * UniqueId filter form base class.
 *
 * @package    filters
 * @subpackage UniqueId *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class BaseUniqueIdFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'last_id'    => new sfWidgetFormFilterInput(),
      'table_name' => new sfWidgetFormFilterInput(),
      'field_name' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'last_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'table_name' => new sfValidatorPass(array('required' => false)),
      'field_name' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('unique_id_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'UniqueId';
  }

  public function getFields()
  {
    return array(
      'id'         => 'Number',
      'last_id'    => 'Number',
      'table_name' => 'Text',
      'field_name' => 'Text',
    );
  }
}