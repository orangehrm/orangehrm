<?php

/**
 * Project form base class.
 *
 * @package    form
 * @subpackage project
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseProjectForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'project_id'  => new sfWidgetFormInputHidden(),
      'customer_id' => new sfWidgetFormInputHidden(),
      'deleted'     => new sfWidgetFormInput(),
      'name'        => new sfWidgetFormInput(),
      'description' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'project_id'  => new sfValidatorDoctrineChoice(array('model' => 'Project', 'column' => 'project_id', 'required' => false)),
      'customer_id' => new sfValidatorDoctrineChoice(array('model' => 'Project', 'column' => 'customer_id', 'required' => false)),
      'deleted'     => new sfValidatorInteger(array('required' => false)),
      'name'        => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'description' => new sfValidatorString(array('max_length' => 250, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('project[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Project';
  }

}
