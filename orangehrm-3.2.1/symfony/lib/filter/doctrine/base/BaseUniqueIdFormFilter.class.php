<?php

/**
 * UniqueId filter form base class.
 *
 * @package    orangehrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseUniqueIdFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'last_id'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'dbTable'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'field_name' => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'last_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'dbTable'    => new sfValidatorPass(array('required' => false)),
      'field_name' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('unique_id_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

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
      'dbTable'    => 'Text',
      'field_name' => 'Text',
    );
  }
}
