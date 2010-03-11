<?php

/**
 * ProjectActivity form base class.
 *
 * @package    form
 * @subpackage project_activity
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseProjectActivityForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'activity_id' => new sfWidgetFormInputHidden(),
      'project_id'  => new sfWidgetFormDoctrineChoice(array('model' => 'Project', 'add_empty' => false)),
      'deleted'     => new sfWidgetFormInput(),
      'name'        => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'activity_id' => new sfValidatorDoctrineChoice(array('model' => 'ProjectActivity', 'column' => 'activity_id', 'required' => false)),
      'project_id'  => new sfValidatorDoctrineChoice(array('model' => 'Project')),
      'deleted'     => new sfValidatorInteger(array('required' => false)),
      'name'        => new sfValidatorString(array('max_length' => 100, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('project_activity[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProjectActivity';
  }

}
